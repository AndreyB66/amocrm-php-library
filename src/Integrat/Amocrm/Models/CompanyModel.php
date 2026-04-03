<?php

namespace Integrat\Amocrm\Models;

/**
 * @property int $id
 * @property string $name
 * @property int $responsible_user_id
 * @property int $group_id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $closest_task_at
 * @property bool $is_deleted
 * @property array $custom_fields_values
 * @property int $account_id
 * @property array $_embedded
 */
class CompanyModel
{
    private array $attributes = [];
    
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }
    
    public function hydrate(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }
        
        return $this;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Преобразовать объект в массив
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Получить все теги в виде массива
     * @return array
     */
    public function getTags(): array
    {
        return $this->_embedded['tags'] ?? [];
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
     * Получить все кастомные поля
     * @return array
     */
    public function getCustomFields(): array
    {
        return $this->custom_fields_values ?? [];
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