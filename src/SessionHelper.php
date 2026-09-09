<?php

declare(strict_types=1);

namespace App;

class SessionHelper
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name(Сonfig::sessionName());
            session_start();
        }
    }

    public static function currentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function setUserId(int $userId): void
    {
        $_SESSION['user_id'] = $userId;
    }

    public static function isLoggedIn(): bool
    {
        return self::currentUserId() !== null;
    }

    public static function loginUser(User $user): void
    {
        session_regenerate_id(true);
        self::setUserId($user->id);
    }

    public static function logout(): void
    {
        self::destroy();
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 3600,
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                ]
            );
        }

        session_destroy();
    }
}
