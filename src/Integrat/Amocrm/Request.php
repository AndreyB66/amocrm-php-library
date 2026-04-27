<?php

namespace Integrat\Amocrm;

use GuzzleHttp\Client;

class Request
{
    private Client $httpClient;

    public function __construct(string $domain, string $apiKey)
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://' . $domain . '/api/v4/',
            'timeout'  => 30,
            'connect_timeout' => 10,
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
            'debug' => false,
        ]);
    }

    private function send(string $method, string $endpoint, array $data = []): ?array
    {
        $options = [];
        if (!empty($data)) {
            $options['json'] = $data;
        }

        $lastException = null;
        $maxAttempts = 3;
        $retryDelay = 2;

        for ($i = 0; $i < $maxAttempts; $i++) {
            try {
                $response = $this->httpClient->request($method, $endpoint, $options);
                $statusCode = $response->getStatusCode();

                if ($statusCode === 404) {
                    return null;
                }

                if ($statusCode === 204) {
                    return [];
                }

                if ($statusCode >= 200 && $statusCode < 300) {
                    $body = $response->getBody()->getContents();
                    $decoded = json_decode($body, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception(
                            "Ошибка декодирования JSON: " . json_last_error_msg()
                        );
                    }
                    
                    return $decoded;
                }

                throw new \Exception("Неожиданный статус код: {$statusCode}");

            } catch (\Exception $e) {
                $lastException = $e;
                if ($i >= $maxAttempts - 1) {
                    break;
                }
                sleep($retryDelay);
            }
        }

        throw new \Exception(
            "Было сделано {$maxAttempts} попыток к API amoCRM, которые закончились неудачей: {$method} {$endpoint}. "
            . "Последняя ошибка: " . ($lastException ? $lastException->getMessage() : 'неизвестна'),
            0,
            $lastException
        );
    }

    public function post(string $endpoint, array $data): ?array
    {
        return $this->send('POST', $endpoint, $data);
    }

    public function get(string $endpoint): ?array
    {
        return $this->send('GET', $endpoint);
    }

    public function patch(string $endpoint, array $data): ?array
    {
        return $this->send('PATCH', $endpoint, $data);
    }
}