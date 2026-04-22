<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class NoteService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(string $entityType, int $entityId, array $data): array
    {
        if (!in_array($entityType, ['leads', 'contacts', 'companies'])) {
            throw new \InvalidArgumentException('Передан не верный тип сущности для прикрепления примечания');
        }

        $result = $this->request->post('/' . $entityType . '/' . $entityId. '/notes', $data);

        if (empty($result['_embedded']['notes'][0]['id'])) {
            throw new \Exception(
                "Не удалось создать примечание для сущности $entityType c ID $entityId с данными: " . print_r($data, true)
            );
        }

        return $result;
    }

    public function updateById(string $entityType, int $entityId, int $noteId, array $data): array
    {
        if (!in_array($entityType, ['leads', 'contacts', 'companies'])) {
            throw new \InvalidArgumentException('Передан не верный тип сущности для прикрепления примечания');
        }

        $result = $this->request->post('/' . $entityType . '/' . $entityId. '/notes/' . $noteId, $data);

        if (empty($result['_embedded']['notes'][0]['id'])) {
            throw new \Exception(
                "Не удалось обновить примечание с $noteId для сущности $entityType c ID $entityId с данными: " . print_r($data, true)
            );
        }

        return $result;
    }
}