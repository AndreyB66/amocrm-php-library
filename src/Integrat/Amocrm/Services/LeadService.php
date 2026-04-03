<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Models\LeadModel;
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

    public function findById(int $id): ?LeadModel
    {
        $result = $this->request->get('/leads/' . $id);

        if (empty($result)) {
            return null;
        }

        return new LeadModel($result);
    }

    public function findByField(string $fieldValue): array
    {
        $result = $this->request->get('/leads?query=' . $fieldValue);

        if (empty($result) || empty($result['_embedded']['leads'])) {
            return [];
        }

        $arrayModels = [];
        foreach($result['_embedded']['leads'] as $lead) {
            $arrayModels[] = new LeadModel($lead);
        }

        return $arrayModels;
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