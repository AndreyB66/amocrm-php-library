<?php

namespace Integrat\Amocrm\Repositories;

use Integrat\Amocrm\Models\CompanyModel;
use Integrat\Amocrm\Models\ContactModel;
use Integrat\Amocrm\Models\LeadModel;

class ContactRepository extends AbstractRepository
{
    public function create(array $data): int
    {
        $result = $this->request->post('/contacts', $data);

        if (empty($result['_embedded']['contacts'][0]['id'])) {
            throw new \Exception(
                'Не удалось создать контакт. Входящие данные: ' . print_r($data, true) . 'Ответ: ' . print_r($result, true)
            );
        }

        return $result['_embedded']['contacts'][0]['id'];
    }

    public function update(array $data): int
    {
        $result = $this->request->patch('/contacts', $data);

        if (empty($result['_embedded']['contacts'][0]['id'])) {
            throw new \Exception(
                'Не удалось обновить контакт. Входящие данные: ' . print_r($data, true) . 'Ответ: ' . print_r($result, true)
            );
        }

        return $result['_embedded']['contacts'][0]['id'];
    }

    /**
     * Получить контакт по ID
     * @param int $contactId
     * @return ContactModel|null
     */
    public function findById(int $contactId): ?ContactModel
    {
        $result = $this->request->get('/contacts/' . $contactId . '?with=leads,companies');
        return empty($result) ? null : new ContactModel($result);
    }

    /**
     * Найти контакт/контакты по полю
     * @param string $fieldValue
     * @return ContactModel[]
     */
    public function findByField(string $fieldValue): array
    {
        $result = $this->request->get('/contacts?with=leads,companies&query=' . urlencode($fieldValue));
        
        if (empty($result['_embedded']['contacts'])) {
            return [];
        }

        return array_map(
            fn($contact) => new ContactModel($contact),
            $result['_embedded']['contacts']
        );
    }

    public function createLead(int $contactId, array $leadData): ?int
    {
        $result = $this->request->post('/leads', $leadData);
        if (empty($result['_embedded']['leads'][0]['id'])) {
            throw new \Exception("Не удалось создать сделку для контакта {$contactId}. Данные: " . json_encode($leadData));
        }
        
        $createdLeadId = $result['_embedded']['leads'][0]['id'];
        
        $linkResult = $this->request->post('/contacts/' . $contactId . '/link', [[
            'to_entity_id' => $createdLeadId,
            'to_entity_type' => 'leads'
        ]]);
        
        if (empty($linkResult['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception("Не удалось связать сделку $createdLeadId с контактом $contactId");
        }
        
        return $createdLeadId;
    }

    /**
     * Получить все сделки контакта
     * @param int $contactId
     * @return LeadModel[]
     */
    public function findAllLeads(int $contactId): array
    {
        return $this->findRelatedEntities($contactId, 'contacts', 'leads', LeadModel::class);
    }

    /**
     * Получить все активные сделки контакта
     * @param int $contactId
     * @param array $closedStatusIds
     * @return LeadModel[]
     */
    public function findActiveLeads(int $contactId, array $closedStatusIds = [142, 143]): array
    {
        $leads = $this->findAllLeads($contactId);
        
        if (empty($closedStatusIds)) {
            return $leads;
        }
        
        return array_filter($leads, function($lead) use ($closedStatusIds) {
            /** @var LeadModel $lead */
            return !in_array($lead->statusId, $closedStatusIds);
        });
    }

    public function findCompany(int $contactId): ?CompanyModel
    {
        return $this->findFirstRelatedEntity($contactId, 'contacts', 'companies', CompanyModel::class);
    }

    /**
     * Получить массив контактов за 1 запрос
     * @param array $contactIds
     * @return ContactModel[]
     */
    public function findBulk(array $contactIds): array
    {
        return $this->loadEntitiesByIds('contacts', $contactIds, ContactModel::class);
    }
}