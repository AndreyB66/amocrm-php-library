<?php

namespace Integrat\Amocrm;

class Request
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $domain, string $apiKey)
    {
        $this->baseUrl = 'https://' . $domain . '/api/v4';
        $this->apiKey = $apiKey;
    }

    private function send(string $method, string $endpoint, array $data = []): ?array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        $ch = curl_init();
        
        // Базовые настройки
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        // Метод запроса
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        // Пробуем 3 раза с задержкой
        for ($i = 0; $i < 3; $i++) {
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Логируем ответ
            // error_log(print_r(curl_getinfo($ch), true));
            // error_log(print_r($response, true));

            if ($httpCode == 400 || $httpCode == 401 || $httpCode == 403) {
                throw new \Exception(print_r($response, true));
            }

            if ($response !== false && $httpCode < 500) {
                curl_close($ch);
                return json_decode($response, true);
            }
            
            sleep(2); // ждем 2 секунды перед повтором
        }
        
        curl_close($ch);
        return null;
    }

    public function post(string $endpoint, array $data): ?array
    {
        return $this->send('POST', $endpoint, $data);
    }

    public function get(string $endpoint, array $params = []): ?array
    {
        return $this->send('GET', $endpoint, $params);
    }

    public function patch(string $endpoint, array $data): ?array
    {
        return $this->send('PATCH', $endpoint, $data);
    }
}