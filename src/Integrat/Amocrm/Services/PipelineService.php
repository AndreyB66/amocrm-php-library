<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class PipelineService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getAll(): array
    {
        $result = $this->request->get('/leads/pipelines');

        if (!empty($result)) {
            return $result;
        }

        return [];
    }
}