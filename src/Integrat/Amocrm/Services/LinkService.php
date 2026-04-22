<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class LinkService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function linkContactToLead(int $contactId, int $leadId): array
    {
        $data = [
            [
                'to_entity_id' => $leadId,
                'to_entity_type' => 'leads'
            ]
        ];

        $result = $this->request->post('/contacts/' . $contactId . '/link', $data);

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception(
                "Не удалось привязать контакт $contactId к сделке $leadId"
            );
        }

        return $result;
    }

    public function linkContactToCompany(int $contactId, int $companyId): array
    {
        $data = [
            [
                'to_entity_id' => $companyId,
                'to_entity_type' => 'companies'
            ]
        ];

        $result = $this->request->post('/contacts/' . $contactId . '/link', $data);

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception(
                "Не удалось привязать контакт $contactId к компании $companyId"
            );
        }

        return $result;
    }

    public function linkLeadToContact(int $leadId, int $contactId): array
    {
        $data = [
            [
                'to_entity_id' => $contactId,
                'to_entity_type' => 'contacts'
            ]
        ];

        $result = $this->request->post('/leads/' . $leadId . '/link', $data);

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception(
                "Не удалось привязать сделку $leadId к контакту $contactId"
            );
        }

        return $result;
    }

    public function linkLeadToCompany(int $leadId, int $companyId): array
    {
        $data = [
            [
                'to_entity_id' => $companyId,
                'to_entity_type' => 'companies'
            ]
        ];

        $result = $this->request->post('/leads/' . $leadId . '/link', $data);

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception(
                "Не удалось привязать сделку $leadId к компании $companyId"
            );
        }

        return $result;
    }

    public function linkCompanyToLead(int $companyId, int $leadId): array
    {
        $data = [
            [
                'to_entity_id' => $leadId,
                'to_entity_type' => 'leads'
            ]
        ];

        $result = $this->request->post('/companies/' . $companyId . '/link', $data);

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception(
                "Не удалось привязать компанию $companyId к сделке $leadId"
            );
        }

        return $result;
    }

    public function linkCompanyToContact(int $companyId, int $contactId): array
    {
        $data = [
            [
                'to_entity_id' => $contactId,
                'to_entity_type' => 'contacts'
            ]
        ];

        $result = $this->request->post('/companies/' . $companyId . '/link', $data);

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            throw new \Exception(
                "Не удалось привязать компанию $companyId к контакту $contactId"
            );
        }

        return $result;
    }

    public function getLeadLinks(int $id): array
    {
        $result = $this->request->get('/leads/' . $id . '/links');

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            return [];
        }

        return $result;
    }

    public function getContactLinks(int $id): array
    {
        $result = $this->request->get('/contacts/' . $id . '/links');

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            return [];
        }

        return $result;
    }

    public function getCompanyLinks(int $id): array
    {
        $result = $this->request->get('/companies/' . $id . '/links');

        if (empty($result['_embedded']['links'][0]['entity_id'])) {
            return [];
        }

        return $result;
    }

    public function unlinkContactToLead(int $contactId, int $leadId): void
    {
        $data = [
            [
                'to_entity_id' => $leadId,
                'to_entity_type' => 'leads'
            ]
        ];

        $this->request->post('/contacts/' . $contactId . '/unlink', $data);
    }

    public function unlinkContactToCompany(int $contactId, int $companyId): void
    {
        $data = [
            [
                'to_entity_id' => $companyId,
                'to_entity_type' => 'companies'
            ]
        ];

        $this->request->post('/contacts/' . $contactId . '/unlink', $data);
    }

    public function unlinkLeadToContact(int $leadId, int $contactId): void
    {
        $data = [
            [
                'to_entity_id' => $contactId,
                'to_entity_type' => 'contacts'
            ]
        ];

        $this->request->post('/leads/' . $leadId . '/unlink', $data);
    }

    public function unlinkLeadToCompany(int $leadId, int $companyId): void
    {
        $data = [
            [
                'to_entity_id' => $companyId,
                'to_entity_type' => 'companies'
            ]
        ];

        $this->request->post('/leads/' . $leadId . '/unlink', $data);
    }

    public function unlinkCompanyToLead(int $companyId, int $leadId): void
    {
        $data = [
            [
                'to_entity_id' => $leadId,
                'to_entity_type' => 'leads'
            ]
        ];

        $this->request->post('/companies/' . $companyId . '/unlink', $data);
    }

    public function unlinkCompanyToContact(int $companyId, int $contactId): void
    {
        $data = [
            [
                'to_entity_id' => $contactId,
                'to_entity_type' => 'contacts'
            ]
        ];

        $this->request->post('/companies/' . $companyId . '/unlink', $data);
    }
}