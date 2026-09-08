<?php

declare(strict_types=1);

namespace App;

use Exception;

final class AuthService
{
    public static function register(
        string $name,
        string $phone,
        string $email,
        string $password
    ): User
    {
        $phone = Validator::normalizePhone($phone);
        $email = mb_strtolower($email);

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare('SELECT phone, email FROM users WHERE phone = ? OR email = ?');
        $stmt->execute([$phone, $email]);
        $userFinded = $stmt->fetch();

        if (!$userFinded) {
            if($userFinded['phone'] === $phone) {
                throw new Exception("Пользователь с таким номером телефона уже существует.");
            }
            if($userFinded['email'] === $email) {
                throw new Exception('Пользователь с таким email адресом уже существует.');
            }
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $email, $hash]);

        $id = (int) $pdo->lastInsertId();
        return new User ($id,$name,$phone,$email);

    }

    public static function login()
    {

    }

    public static function getById()
    {

    }

    public static function updateProfile()
    {

    }
}