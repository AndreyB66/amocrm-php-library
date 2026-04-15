<?php

namespace Integrat\Amocrm\Services;

use Integrat\Amocrm\Request;

class UserService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getAll(): array
    {
        $result = $this->request->get('/users');

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * Получить всех активных пользователей
     * @return array
     */
    public function getAllActive(): array
    {
        $allActiveUsers = [];
        $page = 1;
        $limit = 250;
        
        while (true) {
            $result = $this->request->get("/users?page={$page}&limit={$limit}");
            
            // Если ответ пустой или нет пользователей - выходим
            if (empty($result) || empty($result['_embedded']['users'])) {
                break;
            }
            
            // Фильтруем только активных пользователей
            foreach ($result['_embedded']['users'] as $user) {
                if (isset($user['rights']['is_active']) && $user['rights']['is_active'] === true) {
                    $allActiveUsers[] = $user;
                }
            }
            
            // Проверяем наличие следующей страницы через _links
            if (empty($result['_links']['next'])) {
                break;
            }
            
            $page++;
        }
        
        return $allActiveUsers;
    }

    public function getById(int $id): array
    {
        $result = $this->request->get('/users/' . $id);

        if (!empty($result)) {
            return $result;
        }

        return [];
    }
}