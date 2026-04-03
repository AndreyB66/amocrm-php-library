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
        
        while ($page < 15) {
            $result = $this->request->get("/users?page={$page}&limit=250");
            
            if (empty($result) || empty($result['_embedded']['users'])) {
                break;
            }
            
            foreach ($result['_embedded']['users'] as $user) {
                // Пропускаем деактивированных пользователей
                if (isset($user['rights']['is_active']) && $user['rights']['is_active'] == false) {
                    continue;
                }
                
                $allActiveUsers[] = $user;
            }
            
            // Проверяем наличие следующей страницы
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