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
        }

        // Пробуем 3 раза с задержкой
        for ($i = 0; $i < 3; $i++) {
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // 404 при GET-запросах - возвращаем пустой массив
            if ($httpCode === 404 && $method === 'GET') {
                curl_close($ch);
                return [];
            }

            // Клиентские ошибки (4xx) и ошибки сервера (5xx) сразу прерываем
            if ($httpCode >= 400) {
                curl_close($ch);
                throw new \Exception("HTTP $httpCode: $response");
            }

            // Успешный ответ
            if ($response !== false) {
                curl_close($ch);
                return json_decode($response, true);
            }
            
            sleep(2);
        }

        curl_close($ch);
        throw new \Exception("Было сделано 3 попытки к API amoCRM, которые закончились неудачей: {$method} {$endpoint}");
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