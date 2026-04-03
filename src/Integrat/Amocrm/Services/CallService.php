<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class CallService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(array $data): array
    {
        $result = $this->request->post('/calls', $data);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * Исходящий звонок
     * @param int $duration // Длительность звонка
     * @param string $clientNumberPhone // Номер клиента
     * @param string $recordLink // Ссылка на запись разговора
     * @param string $result // Статус завершения звонка (Answer, busy ...)
     * @param int $responsibleUserId // ID ответственного из amoCRM
     * @return array
     */
    public function addOutbound(
        int $duration,
        string $clientNumberPhone,
        string $recordLink,
        string $result,
        int $responsibleUserId
    ): array {
        $newCall = [
            [
                'duration' => $duration,
                'source' => 'custom_integration',
                'phone' => $clientNumberPhone,
                'link' => $recordLink,
                'direction' => 'outbound',
                'call_result' => $result,
                'call_responsible' => $responsibleUserId
            ]
        ];

        $saveCall = $this->create($newCall);
        if (empty($saveCall)) {
            return [];
        }

        return $saveCall;
    }

    /**
     * Входящий звонок
     * @param int $duration // Длительность звонка
     * @param string $clientNumberPhone // Номер клиента
     * @param string $recordLink // Ссылка на запись разговора
     * @param string $result // Статус завершения звонка (Answer, busy ...)
     * @param int $responsibleUserId // ID ответственного из amoCRM
     * @return array
     */
    public function addInbound(
        int $duration,
        string $clientNumberPhone,
        string $recordLink,
        string $result,
        int $responsibleUserId
    ): array {
        $newCall = [
            [
                'duration' => $duration,
                'source' => 'custom_integration',
                'phone' => $clientNumberPhone,
                'link' => $recordLink,
                'direction' => 'inbound',
                'call_result' => $result,
                'call_responsible' => $responsibleUserId
            ]
        ];

        $saveCall = $this->create($newCall);
        if (empty($saveCall)) {
            return [];
        }

        return $saveCall;
    }
}