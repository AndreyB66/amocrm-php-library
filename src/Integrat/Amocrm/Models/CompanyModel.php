<?php

namespace Integrat\Amocrm\Models;

class CompanyModel
{
    public ?int $id;
    public ?string $name;
    public ?int $responsibleUserId;
    public ?int $groupId;
    public ?int $createdBy;
    public ?int $updatedBy;
    public ?int $createdAt;
    public ?int $updatedAt;
    public ?int $closestTaskAt;
    public ?bool $isDeleted;
    public ?array $customFieldsValues;
    public ?int $accountId;
    public ?array $links;
    public ?array $embedded;
    
    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->responsibleUserId = $data['responsible_user_id'] ?? null;
        $this->groupId = $data['group_id'] ?? null;
        $this->createdBy = $data['created_by'] ?? null;
        $this->updatedBy = $data['updated_by'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->closestTaskAt = $data['closest_task_at'] ?? null;
        $this->isDeleted = $data['is_deleted'] ?? false;
        $this->customFieldsValues = $data['custom_fields_values'] ?? null;
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
            'responsible_user_id' => $this->responsibleUserId,
            'group_id' => $this->groupId,
            'created_by' => $this->createdBy,
            'updated_by' => $this->updatedBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'closest_task_at' => $this->closestTaskAt,
            'is_deleted' => $this->isDeleted,
            'custom_fields_values' => $this->customFieldsValues,
            'account_id' => $this->accountId,
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
     * Получить ID связанных сделок
     * @return array
     */
    public function getLeadIds(): array
    {
        $leads = $this->embedded['leads'] ?? [];
        return array_column($leads, 'id');
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