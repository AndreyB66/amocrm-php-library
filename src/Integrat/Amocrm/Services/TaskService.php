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

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function getById(int $id): array
    {
        $result = $this->request->get('/tasks/' . $id);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function update(array $data): array
    {
        $result = $this->request->patch('/tasks', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }    
}