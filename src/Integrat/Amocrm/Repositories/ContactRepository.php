<?php

namespace Integrat\Amocrm\Repositories;

use Integrat\Amocrm\Models\CompanyModel;
use Integrat\Amocrm\Models\ContactModel;
use Integrat\Amocrm\Models\LeadModel;
use Integrat\Amocrm\Request;

class ContactRepository
{
    private const CHUNK_SIZE = 50;
    private const PAGE_LIMIT = 50;
    private const CLOSED_STATUSES = [142, 143];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Создание контакта
     * @param array $data [['responsible_user_id' => 876635, ...]]
     * @return int
     */
    public function create(array $data): int
    {
        $result = $this->request->post('/contacts', $data);

        if (empty($result)) {
            throw new \Exception('Не удалось создать контакт по данным: ' . print_r($data, true) . 'Ошибка: ' . $result);
        }

        return $result['_embedded']['contacts'][0]['id'];
    }

    /**
     * Обновление контакта
     * @param array $data [['id' => 665325, 'responsible_user_id' => 876635, ...]]
     * @return array
     */
    public function update(array $data): array
    {
        $result = $this->request->patch('/contacts', $data);

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

    /**
     * Создать сделку для контакта
     * @param int $contactId
     * @param array $leadData [['pipeline_id' => 6725478, 'status_id' => 56919070 ...]]
     * @return int|null
     */
    public function createLead(int $contactId, array $leadData): ?int
    {
        // Создаем сделку
        $result = $this->request->post('/leads', $leadData);
        
        if (empty($result['_embedded']['leads'][0])) {
            return null;
        }
        
        $createdLeadId = $result['_embedded']['leads'][0]['id'];
        
        // Связываем с контактом
        $linkData = [[
            'to_entity_id' => $createdLeadId,
            'to_entity_type' => 'leads'
        ]];
        
        $this->request->post('/contacts/' . $contactId . '/link', $linkData);
        
        return $createdLeadId;
    }

    public function findAllLeads(int $contactId): array
    {
        $result = $this->request->get('/contacts/' . $contactId . '/links');
        if (empty($result['_embedded']['links'])) {
            return [];
        }

        $leadIds = [];
        foreach ($result['_embedded']['links'] as $entity) {
            if ($entity['to_entity_type'] == 'leads') {
                $leadIds[] = $entity['to_entity_id'];
            }
        }
        
        if (empty($leadIds)) {
            return [];
        }

        // Разбиваем ID сделок на части по CHUNK_SIZE штук
        $leadIdChunks = array_chunk($leadIds, self::CHUNK_SIZE);
        $leadModels = [];

        // Для каждого чанка делаем отдельный запрос
        foreach ($leadIdChunks as $chunkLeadIds) {
            // Формируем начальный URL для первого запроса текущего чанка
            $url = '/leads?page=1&limit=' . self::PAGE_LIMIT;
            
            foreach ($chunkLeadIds as $leadId) {
                $url .= '&id[]=' . $leadId;
            }
            
            while (true) {
                $result = $this->request->get($url);
                
                if (empty($result) || !isset($result['_embedded']['leads'])) {
                    break;
                }
                
                foreach ($result['_embedded']['leads'] as $lead) {
                    if (!empty($lead)) {
                        $leadModels[] = new LeadModel($lead);
                    }
                }
                
                // Работа со следующей страницей
                if (empty($result['_links']['next'])) {
                    break;
                } else {
                    // Используем следующий URL из ответа API
                    $fullUrl = $result['_links']['next']['href'];
                    $url = parse_url($fullUrl, PHP_URL_PATH);
                    if ($query = parse_url($fullUrl, PHP_URL_QUERY)) {
                        $url .= '?' . $query;
                    }
                }
            }
        }

        return $leadModels;
    }

    public function findActiveLead(int $contactId): array
    {
        // Получаем все сделки
        /** @var LeadModel[] $leads */
        $leads = $this->findAllLeads($contactId);

        // Выбираем только активные сделки
        $leadModels = [];
        foreach ($leads as $lead) {
            if (empty($lead)) {
                continue;
            }

            if (in_array($lead->status_id, self::CLOSED_STATUSES)) {
                continue;
            }

            $leadModels[] = $lead;
        }

        return $leadModels;
    }

    public function findCompany(int $contactId): ?CompanyModel
    {
        // Получаем все связи
        $result = $this->request->get('/contacts/' . $contactId . '/links');

        if (empty($result['_embedded']['links'])) {
            return null;
        }

        // Вычленяем только компании
        $companyId = 0;
        foreach ($result['_embedded']['links'] as $entity) {
            if ($entity['to_entity_type'] == 'companies') {
                $companyId = $entity['to_entity_id'];
            }
        }
        
        if (empty($companyId)) {
            return null;
        }

        // Получаем всю компанию отдельным запросом
        $result = $this->request->get('/companies/' . $companyId);

        if (empty($result['id'])) {
            return null;
        }

        return new CompanyModel($result);
    }
}