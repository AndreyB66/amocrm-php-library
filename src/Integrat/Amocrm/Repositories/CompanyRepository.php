<?php

namespace Integrat\Amocrm\Repositories;

use Integrat\Amocrm\Models\CompanyModel;
use Integrat\Amocrm\Models\ContactModel;
use Integrat\Amocrm\Models\LeadModel;

class CompanyRepository extends AbstractRepository
{
    public function create(array $data): int
    {
        $result = $this->request->post('/companies', $data);

        if (empty($result['_embedded']['companies'][0]['id'])) {
            throw new \Exception(
                'Не удалось создать компанию. Входящие данные: ' . print_r($data, true) . 'Ответ: ' . print_r($result, true)
            );
        }
        
        return $result['_embedded']['companies'][0]['id'];
    }

    public function update(array $data): int
    {
        $result = $this->request->patch('/companies', $data);

        if (empty($result['_embedded']['companies'][0]['id'])) {
            throw new \Exception(
                'Не удалось обновить компанию. Входящие данные: ' . print_r($data, true) . 'Ответ: ' . print_r($result, true)
            );
        }

        return $result['_embedded']['companies'][0]['id'];
    }

    /**
     * Получить компанию по ID
     * @param int $companyId
     * @return CompanyModel|null
     */
    public function findById(int $companyId): ?CompanyModel
    {
        $result = $this->request->get('/companies/' . $companyId . '?with=leads,contacts');
        return empty($result) ? null : new CompanyModel($result);
    }

    /**
     * Найти компанию/компании по полю
     * @param string $fieldValue
     * @return CompanyModel[]
     */
    public function findByField(string $fieldValue): array
    {
        $result = $this->request->get('/companies?with=leads,contacts&query=' . urlencode($fieldValue));
        
        if (empty($result['_embedded']['companies'])) {
            return [];
        }

        return array_map(
            fn($company) => new CompanyModel($company),
            $result['_embedded']['companies']
        );
    }

    public function createLead(int $companyId, array $leadData): ?int
    {
        $result = $this->request->post('/leads', $leadData);
        if (empty($result['_embedded']['leads'][0]['id'])) {
            return null;
        }
        
        $createdLeadId = $result['_embedded']['leads'][0]['id'];
        
        $linkResult = $this->request->post('/companies/' . $companyId . '/link', [[
            'to_entity_id' => $createdLeadId,
            'to_entity_type' => 'leads'
        ]]);
        
        if (empty($linkResult['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception("Не удалось связать сделку {$createdLeadId} с компанией {$companyId}");
        }
        
        return $createdLeadId;
    }

    /**
     * Получить все сделки компании
     * @param int $companyId
     * @return LeadModel[]
     */
    public function findAllLeads(int $companyId): array
    {
        return $this->findRelatedEntities($companyId, 'companies', 'leads', LeadModel::class);
    }

    /**
     * Получить все контакты компании
     * @param int $companyId
     * @return ContactModel[]
     */
    public function findAllContacts(int $companyId): array
    {
        return $this->findRelatedEntities($companyId, 'companies', 'contacts', ContactModel::class);
    }

    /**
     * Получить все активные сделки компании
     * @param int $companyId
     * @param array $closedStatusIds
     * @return LeadModel[]
     */
    public function findActiveLeads(int $companyId, array $closedStatusIds = [142, 143]): array
    {
        $leads = $this->findAllLeads($companyId);
        
        if (empty($closedStatusIds)) {
            return $leads;
        }
        
        return array_filter($leads, function($lead) use ($closedStatusIds) {
            /** @var LeadModel $lead */
            return !in_array($lead->statusId, $closedStatusIds);
        });
    }

    /**
     * Получить массив компаний за 1 запрос
     * @param array $companyIds
     * @return CompanyModel[]
     */
    public function findBulk(array $companyIds): array
    {
        return $this->loadEntitiesByIds('companies', $companyIds, CompanyModel::class);
    }
}