<?php

declare(strict_types=1);

namespace App;

final class Config
{
    public static function dbHost(): string
    {
        return getenv('DB_HOST');
    }

    public static function dbName(): string
    {
        return getenv('DB_NAME');
    }

    public static function dbUser(): string
    {
        return getenv('DB_USER');
    }

    public static function dbPass():string
    {
        return getenv('DB_PASS');
    }

    public static function smartcaptchaClientKey(): string
    {
        return getenv('SMARTCAPTCHA_CLIENT_KEY');
    }

    public static function smartcaptchaSecretKey(): string
    {
        return getenv('SMARTCAPTCHA_SECRET_KEY');
    }

    public static function sessionName():string
    {
        return 'app_session';
    }
}