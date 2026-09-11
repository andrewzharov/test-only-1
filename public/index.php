<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\SessionHelper;
use App\AuthService;
use App\Helpers;

SessionHelper::start();

$userId = SessionHelper::currentUserId();
$user = null;
// Если в сессии хранится ID пользователя, то проверяем в базе
if ($userId !== null) {
    $user = AuthService::getById($userId);
    //Если пользователя нет в базе
    if ($user === null) {
        SessionHelper::logout();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h1>Главная страница</h1>

    <?php if ($user): ?>
        <p>Привет, <?= Helpers::e($user->getDisplayName()) ?>!</p>

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
