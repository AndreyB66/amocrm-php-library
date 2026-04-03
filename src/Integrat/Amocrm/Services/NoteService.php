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
            throw new \Exception('Передан не верный тип сущности для прикрепления примечания');
        }

        $result = $this->request->post('/' . $entityType . '/' . $entityId. '/notes', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }
}