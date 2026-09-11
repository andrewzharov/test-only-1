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
        if ($userFinded) {
            if($userFinded['phone'] === $phone) {
                throw new Exception("Пользователь с таким номером телефона уже существует");
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

    public static function login(string $loginRaw, string $password): ?User
    {
        $login = mb_strtolower($loginRaw);
        $phone = Validator::normalizePhone($loginRaw);

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT id, name, phone, email, password FROM users WHERE email = ? OR phone = ? LIMIT 1'
        );
        $stmt->execute([$login, $phone]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($password, $row['password'])) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['name'],
            $row['phone'],
            $row['email'],
        );
    }

    public static function getById(int $userId): ?User
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT id, name, phone, email FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['name'],
            $row['phone'],
            $row['email'],
        );
    }

    public static function updateProfile(
        int $userId,
        string $name,
        string $phoneRaw,
        string $emailRaw,
        ?string $newPassword
    ): void {
        $phone = Validator::normalizePhone($phoneRaw);
        $email = mb_strtolower($emailRaw);

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT phone, email FROM users WHERE (phone = ? OR email = ?) AND id != ? LIMIT 1'
        );
        $stmt->execute([$phone, $email, $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ($existing['phone'] === $phone) {
                throw new Exception('Этот телефон уже используется другим пользователем.');
            }
            if ($existing['email'] === $email) {
                throw new Exception('Этот email уже используется другим пользователем.');
            }
        }

        if ($newPassword !== null && $newPassword !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'UPDATE users SET name = ?, phone = ?, email = ?, password = ? WHERE id = ?'
            );
            $stmt->execute([$name, $phone, $email, $hash, $userId]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?'
            );
            $stmt->execute([$name, $phone, $email, $userId]);
        }
    }
}