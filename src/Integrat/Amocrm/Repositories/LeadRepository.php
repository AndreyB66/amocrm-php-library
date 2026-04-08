<?php

namespace Integrat\Amocrm\Repositories;

use Integrat\Amocrm\Models\CompanyModel;
use Integrat\Amocrm\Models\ContactModel;
use Integrat\Amocrm\Models\LeadModel;
use Integrat\Amocrm\Request;

class LeadRepository
{
    private const CHUNK_SIZE = 50;
    private const PAGE_LIMIT = 50;
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Создание сделки
     * @param array $data [['responsible_user_id' => 876635, ...]]
     * @return int
     */
    public function create(array $data): int
    {
        $result = $this->request->post('/leads', $data);

        if (empty($result)) {
            throw new \Exception('Не удалось создать сделку по данным: ' . print_r($data, true) . 'Ошибка: ' . $result);
        }

        return $result['_embedded']['leads'][0]['id'];
    }

    /**
     * Обновление сделки
     * @param array $data [['id' => 665325, 'responsible_user_id' => 876635, ...]]
     * @return array
     */
    public function update(array $data): array
    {
        $result = $this->request->patch('/leads', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function findById(int $leadId): ?LeadModel
    {
        $result = $this->request->get('/leads/' . $leadId);

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

    public function findAllContacts(int $leadId): array
    {
        $result = $this->request->get('/leads/' . $leadId . '/links');
        if (empty($result['_embedded']['links'])) {
            return [];
        }

        $contactIds = [];
        foreach ($result['_embedded']['links'] as $entity) {
            if ($entity['to_entity_type'] == 'contacts') {
                $contactIds[] = $entity['to_entity_id'];
            }
        }
        
        if (empty($contactIds)) {
            return [];
        }

        // Разбиваем ID сделок на части по CHUNK_SIZE штук
        $contactIdChunks = array_chunk($contactIds, self::CHUNK_SIZE);
        $models = [];

        // Для каждого чанка делаем отдельный запрос
        foreach ($contactIdChunks as $chunkContactIds) {
            // Формируем начальный URL для первого запроса текущего чанка
            $url = '/contacts?page=1&limit=' . self::PAGE_LIMIT;
            
            foreach ($chunkContactIds as $contactId) {
                $url .= '&id[]=' . $contactId;
            }
            
            while (true) {
                $result = $this->request->get($url);
                
                if (empty($result) || !isset($result['_embedded']['contacts'])) {
                    break;
                }
                
                foreach ($result['_embedded']['contacts'] as $contact) {
                    if (!empty($contact)) {
                        $models[] = new ContactModel($contact);
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

        return $models;
    }

    public function findCompany(int $leadId): ?CompanyModel
    {
        // Получаем все связи
        $result = $this->request->get('/leads/' . $leadId . '/links');

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