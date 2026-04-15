<?php

namespace Integrat\Amocrm\Repositories;

use Integrat\Amocrm\Request;

abstract class AbstractRepository
{
    protected const CHUNK_SIZE = 50;
    protected const PAGE_LIMIT = 50;
    
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Получить все связанные сущности определенного типа
     * 
     * @param int $entityId ID родительской сущности
     * @param string $parentType Тип родительской сущности (leads, contacts, companies)
     * @param string $targetType Тип целевой сущности (leads, contacts, companies)
     * @param string $modelClass Класс модели для создания объектов
     * @return array Массив моделей
     */
    protected function findRelatedEntities(
        int $entityId,
        string $parentType,
        string $targetType,
        string $modelClass
    ): array {
        // Получаем все связи
        $result = $this->request->get("/{$parentType}/{$entityId}/links");
        
        if (empty($result['_embedded']['links'])) {
            return [];
        }

        // Собираем ID целевых сущностей
        $targetIds = [];
        foreach ($result['_embedded']['links'] as $link) {
            if ($link['to_entity_type'] === $targetType) {
                $targetIds[] = $link['to_entity_id'];
            }
        }
        
        if (empty($targetIds)) {
            return [];
        }

        // Загружаем сущности по ID с поддержкой пагинации
        return $this->loadEntitiesByIds($targetType, $targetIds, $modelClass);
    }

    /**
     * Загрузить сущности по списку ID с автоматической разбивкой на чанки и пагинацией
     * 
     * @param string $entityType Тип сущности (leads, contacts, companies)
     * @param array $ids Массив ID для загрузки
     * @param string $modelClass Класс модели
     * @return array Массив моделей
     */
    protected function loadEntitiesByIds(
        string $entityType,
        array $ids,
        string $modelClass
    ): array {
        $models = [];
        $idChunks = array_chunk($ids, self::CHUNK_SIZE);

        foreach ($idChunks as $chunkIds) {
            $url = "/{$entityType}?with=leads,contacts,companies&page=1&limit=" . self::PAGE_LIMIT;
            
            foreach ($chunkIds as $id) {
                $url .= "&id[]={$id}";
            }
            
            while (true) {
                $result = $this->request->get($url);
                
                if (empty($result['_embedded'][$entityType])) {
                    break;
                }
                
                foreach ($result['_embedded'][$entityType] as $entityData) {
                    if (!empty($entityData)) {
                        $models[] = new $modelClass($entityData);
                    }
                }
                
                if (empty($result['_links']['next'])) {
                    break;
                }
                
                $fullUrl = $result['_links']['next']['href'];
                $url = parse_url($fullUrl, PHP_URL_PATH);
                if ($query = parse_url($fullUrl, PHP_URL_QUERY)) {
                    $url .= '?' . $query;
                }
            }
        }

        return $models;
    }

    /**
     * Найти первую связанную сущность определенного типа
     * 
     * @param int $entityId ID родительской сущности
     * @param string $parentType Тип родительской сущности
     * @param string $targetType Тип целевой сущности
     * @param string $modelClass Класс модели
     * @return object|null Модель или null
     */
    protected function findFirstRelatedEntity(
        int $entityId,
        string $parentType,
        string $targetType,
        string $modelClass
    ): ?object {
        $result = $this->request->get("/{$parentType}/{$entityId}/links");
        
        if (empty($result['_embedded']['links'])) {
            return null;
        }

        $targetId = null;
        foreach ($result['_embedded']['links'] as $link) {
            if ($link['to_entity_type'] === $targetType) {
                $targetId = $link['to_entity_id'];
                break;
            }
        }
        
        if (!$targetId) {
            return null;
        }

        $result = $this->request->get("/{$targetType}/{$targetId}");
        
        if (empty($result['id'])) {
            return null;
        }

        return new $modelClass($result);
    }
}