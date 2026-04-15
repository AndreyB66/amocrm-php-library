<?php

namespace Integrat\Amocrm\Repositories;

use Integrat\Amocrm\Models\CompanyModel;
use Integrat\Amocrm\Models\ContactModel;
use Integrat\Amocrm\Models\LeadModel;

class LeadRepository extends AbstractRepository
{
    public function create(array $data): int
    {
        $result = $this->request->post('/leads', $data);
        if (empty($result['_embedded']['leads'][0]['id'])) {
            throw new \Exception('Не удалось создать сделку. Данные: ' . json_encode($data));
        }
        return $result['_embedded']['leads'][0]['id'];
    }

    public function update(array $data): bool
    {
        $result = $this->request->patch('/leads', $data);
        if (empty($result['_embedded']['leads'][0]['id'])) {
            throw new \Exception('Не удалось обновить сделку. Данные: ' . json_encode($data));
        }
        return true;
    }

    public function findById(int $leadId): ?LeadModel
    {
        $result = $this->request->get('/leads/' . $leadId . '?with=contacts,companies');
        return empty($result) ? null : new LeadModel($result);
    }

    public function findByField(string $fieldValue): array
    {
        $result = $this->request->get('/leads?with=contacts,companies&query=' . urlencode($fieldValue));
        
        if (empty($result['_embedded']['leads'])) {
            return [];
        }

        return array_map(
            fn($lead) => new LeadModel($lead),
            $result['_embedded']['leads']
        );
    }

    public function findAllContacts(int $leadId): array
    {
        return $this->findRelatedEntities($leadId, 'leads', 'contacts', ContactModel::class);
    }

    public function findFirstContact(int $leadId): ?ContactModel
    {
        return $this->findFirstRelatedEntity($leadId, 'leads', 'contacts', ContactModel::class);
    }

    public function findCompany(int $leadId): ?CompanyModel
    {
        return $this->findFirstRelatedEntity($leadId, 'leads', 'companies', CompanyModel::class);
    }

    public function findBulkLeads(array $leadIds): array
    {
        return $this->loadEntitiesByIds('leads', $leadIds, LeadModel::class);
    }
}