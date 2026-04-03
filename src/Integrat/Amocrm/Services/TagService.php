<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class TagService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function addForLead(int $leadId, array $tags): array
    {
        $data = [
            "_embedded" => [
                "tags" => []
            ]
        ];
        
        foreach ($tags as $tag) {
            $data["_embedded"]["tags"][] = [
                "name" => $tag
            ];
        }

        $result = $this->request->patch('/leads/' . $leadId, $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function deleteAllForLead(int $leadId): array
    {
        $data = [
            '_embedded' => [
                'tags' => null,
            ]
        ];

        $result = $this->request->patch('/leads/' . $leadId, $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }
}