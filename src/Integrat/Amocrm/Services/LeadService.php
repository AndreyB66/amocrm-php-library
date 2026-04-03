<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class LeadService
{
    private Request $request;
    private LinkService $linkService;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->linkService = new LinkService($this->request);
    }

    public function create(array $data): array
    {
        $result = $this->request->post('/leads', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function getById(int $id): array
    {
        $result = $this->request->get('/leads/' . $id);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function getByField(string $value): array
    {
        $result = $this->request->get('/leads?query=' . $value);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * Обновление
     * @param array $data [['id' => 665325, 'responsible_user_id' => 876635, ...]]
     * @return array|null
     */
    public function update(array $data): array
    {
        $result = $this->request->patch('/leads', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * Получает все связанные сущности
     * @return LinkService
     */
    public function links(): LinkService
    {
        return $this->linkService;
    }
}