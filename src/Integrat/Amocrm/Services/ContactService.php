<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Models\ContactModel;
use Integrat\Amocrm\Request;

class ContactService
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
        $result = $this->request->post('/contacts', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function findById(int $contactId): ?ContactModel
    {
        $result = $this->request->get('/contacts/' . $contactId);

        if (empty($result)) {
            return null;
        }

        return new ContactModel($result);
    }

    public function findByField(string $fieldValue): array
    {
        $result = $this->request->get('/contacts?query=' . $fieldValue);

        if (empty($result) || empty($result['_embedded']['contacts'])) {
            return [];
        }

        $arrayModels = [];
        foreach($result['_embedded']['contacts'] as $contact) {
            $arrayModels[] = new ContactModel($contact);
        }

        return $arrayModels;
    }

    public function findActiveLead(int $contactId): array
    {
        $result = $this->request->get('/contacts/' . $contactId . '/links');
        if (empty($result) || !isset($result['_embedded']['links'])) {
            return [];
        }

        $leads = [];
        foreach ($result['_embedded']['links'] as $entity) {
            if ($entity['to_entity_type'] == 'leads') {
                $leads[] = [
                    'id' => $entity['to_entity_id'],
                    'entity_type' => $entity['to_entity_type']
                ];
            }
        }
        if (empty($leads)) {
            return [];
        }

        $activeLeads = [];
        foreach ($leads as $lead) {
            $getedStatusId = $this->request->get('/leads/' . $lead['id']);
            if (empty($getedStatusId)) {
                continue;
            }
            if ($getedStatusId['status_id'] == 142) {
                continue;
            }
            if ($getedStatusId['status_id'] == 143) {
                continue;
            }
            $activeLeads[] = $lead;
        }

        return $activeLeads;
    }

    public function findAllLead(int $contactId): array
    {
        $result = $this->request->get('/contacts/' . $contactId . '/links');
        if (empty($result) || !isset($result['_embedded']['links'])) {
            return [];
        }

        $leads = [];
        foreach ($result['_embedded']['links'] as $entity) {
            if ($entity['to_entity_type'] == 'leads') {
                $leads[] = [
                    'id' => $entity['to_entity_id'],
                    'entity_type' => $entity['to_entity_type']
                ];
            }
        }
        if (empty($leads)) {
            return [];
        }

        $allLeads = [];
        foreach ($leads as $lead) {
            $fullLead = $this->request->get('/leads/' . $lead['id']);
            
            if (empty($fullLead)) {
                continue;
            }

            $allLeads[] = $fullLead;
        }

        return $allLeads;
    }

    /**
     * Обновление
     * @param array $data [['id' => 665325, 'responsible_user_id' => 876635, ...]]
     * @return array|null
     */
    public function update(array $data): array
    {
        $result = $this->request->patch('/contacts', $data);

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