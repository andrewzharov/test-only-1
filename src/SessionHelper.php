<?php

declare(strict_types=1);

namespace App;

/**
 * Класс Хелпер для работы с сессией
 */
class SessionHelper
{
    /**
     * Метод для запуска сессии
     * @return void
     */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name(Config::sessionName());
            session_start();
        }
    }

    /**
     * Метод возвращает Id текущего пользователя
     * @return int|null
     */
    public static function currentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Метод устанавливает Id пользователя для сессии
     * @param int $userId
     * @return void
     */
    public static function setUserId(int $userId): void
    {
        $_SESSION['user_id'] = $userId;
    }

    /**
     * Метод очистки сессии
     * @return void
     */
    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 3600,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                ]
            );
        }

        session_destroy();
    }
}
