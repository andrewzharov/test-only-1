<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\SessionHelper;
use App\AuthService;

SessionHelper::start();

$userId = SessionHelper::currentUserId();
$user = $userId !== null ? AuthService::getById($userId) : null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="container">
    <h1>Главная страница</h1>

    <?php if ($user): ?>
        <p>Привет, <?= e($user->getDisplayName()) ?>!</p>

        <?php if ($user->isAdmin()): ?>
            <p style="color: red;">Режим администратора</p>
        <?php endif; ?>

        <nav>
            <a href="/profile.php">Профиль</a> |
            <a href="/logout.php">Выйти</a>
        </nav>
    <?php else: ?>
        <nav>
            <a href="/login.php">Войти</a> |
            <a href="/register.php">Регистрация</a>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
