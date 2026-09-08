<?php

declare(strict_types=1);

namespace App;

class Validator
{
    /**
     * Метод валидации телефон, который указал User
     * @param string $phone
     * @return bool
     */
    public static function validatePhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone);
        return $digits !== null && strlen($digits)>=10 && strlen($digits)<=11;
    }

    /**
     * Метод очистки номера телефона от лишних символов
     * @param string $phone
     * @return string
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        return $digits ?? '';
    }

    /**
     * Метод валидации Email, который указал User
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}