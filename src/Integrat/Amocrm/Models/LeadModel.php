<?php

namespace Integrat\Amocrm\Models;

class LeadModel
{
    public ?int $id;
    public ?string $name;
    public ?int $price;
    public ?int $priceWithMinorUnits;
    public ?int $responsibleUserId;
    public ?int $groupId;
    public ?int $statusId;
    public ?int $pipelineId;
    public ?int $lossReasonId;
    public ?int $createdBy;
    public ?int $updatedBy;
    public ?int $createdAt;
    public ?int $updatedAt;
    public ?int $closedAt;
    public ?int $closestTaskAt;
    public ?bool $isDeleted;
    public ?array $customFieldsValues;
    public ?int $score;
    public ?int $accountId;
    public ?int $laborCost;
    public ?bool $isPriceComputed;
    public ?array $links;
    public ?array $embedded;
    
    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->priceWithMinorUnits = $data['price_with_minor_units'] ?? null;
        $this->responsibleUserId = $data['responsible_user_id'] ?? null;
        $this->groupId = $data['group_id'] ?? null;
        $this->statusId = $data['status_id'] ?? null;
        $this->pipelineId = $data['pipeline_id'] ?? null;
        $this->lossReasonId = $data['loss_reason_id'] ?? null;
        $this->createdBy = $data['created_by'] ?? null;
        $this->updatedBy = $data['updated_by'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->closedAt = $data['closed_at'] ?? null;
        $this->closestTaskAt = $data['closest_task_at'] ?? null;
        $this->isDeleted = $data['is_deleted'] ?? null;
        $this->customFieldsValues = $data['custom_fields_values'] ?? null;
        $this->score = $data['score'] ?? null;
        $this->laborCost = $data['labor_cost'] ?? null;
        $this->isPriceComputed = $data['is_price_computed'] ?? null;
        $this->accountId = $data['account_id'] ?? null;
        $this->links = $data['_links'] ?? null;
        $this->embedded = $data['_embedded'] ?? null;
    }

    /**
     * Преобразовать объект в массив
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'price_with_minor_units' => $this->priceWithMinorUnits,
            'responsible_user_id' => $this->responsibleUserId,
            'group_id' => $this->groupId,
            'status_id' => $this->statusId,
            'pipeline_id' => $this->pipelineId,
            'loss_reason_id' => $this->lossReasonId,
            'created_by' => $this->createdBy,
            'updated_by' => $this->updatedBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'closed_at' => $this->closedAt,
            'closest_task_at' => $this->closestTaskAt,
            'is_deleted' => $this->isDeleted,
            'custom_fields_values' => $this->customFieldsValues,
            'score' => $this->score,
            'account_id' => $this->accountId,
            'labor_cost' => $this->laborCost,
            'is_price_computed' => $this->isPriceComputed,
            '_links' => $this->links,
            '_embedded' => $this->embedded,
        ];
    }

    /**
     * Получить все теги в виде массива
     * @return array
     */
    public function getTags(): array
    {
        return $this->embedded['tags'] ?? [];
    }

    /**
     * Получить ID всех тегов
     * @return array
     */
    public function getTagIds(): array
    {
        $tags = $this->getTags();
        return array_column($tags, 'id');
    }

    /**
     * Получить названия всех тегов
     * @return array
     */
    public function getTagNames(): array
    {
        $tags = $this->getTags();
        return array_column($tags, 'name');
    }

    /**
     * Получить ID связанных контактов
     * @return array
     */
    public function getContactIds(): array
    {
        $contacts = $this->embedded['contacts'] ?? [];
        return array_column($contacts, 'id');
    }

    /**
     * Получить ID связанной компании
     * @return ?int
     */
    public function getCompanyId(): ?int
    {
        return $this->embedded['companies'][0]['id'] ?? null;
    }

    /**
     * Получить все кастомные поля
     * @return array
     */
    public function getCustomFields(): array
    {
        return $this->customFieldsValues ?? [];
    }

    /**
     * Получить значение кастомного поля по ID
     * @param int $fieldId
     * @return mixed
     */
    public function getCustomFieldValueById(int $fieldId): mixed
    {
        $fields = $this->getCustomFields();
        foreach ($fields as $field) {
            if ($field['field_id'] === $fieldId) {
                return $field['values'][0]['value'] ?? null;
            }
        }
        return null;
    }
    
    /**
     * Получить значение кастомного поля по имени
     * @param string $fieldName
     * @return mixed
     */
    public function getCustomFieldValueByName(string $fieldName): mixed
    {
        $fields = $this->getCustomFields();
        foreach ($fields as $field) {
            if ($field['field_name'] === $fieldName) {
                return $field['values'][0]['value'] ?? null;
            }
        }
        return null;
    }
}