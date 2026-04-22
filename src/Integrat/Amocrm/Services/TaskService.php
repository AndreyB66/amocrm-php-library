<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class TaskService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(array $data): array
    {
        $result = $this->request->post('/tasks', $data);

        if (empty($result['_embedded']['tasks'][0]['id'])) {
            throw new \Exception(
                "Не удалось создать задачу с переданными данными: " . print_r($data, true)
            );
        }

        return $result;
    }

    public function getById(int $id): array
    {
        $result = $this->request->get('/tasks/' . $id);

        if (empty($result['id'])) {
            return [];
        }

        return $result;
    }

    public function update(array $data): array
    {
        $result = $this->request->patch('/tasks', $data);

        if (empty($result['_embedded']['tasks'][0]['id'])) {
            throw new \Exception(
                "Не удалось обновить задачу с переданными данными: " . print_r($data, true)
            );
        }

        return $result;
    }    
}