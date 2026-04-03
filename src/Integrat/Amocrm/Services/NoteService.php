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
        $result = $this->request->post('/' . $entityType . '/' . $entityId. '/notes', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }
}