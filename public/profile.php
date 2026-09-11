<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\SessionHelper;
use App\AuthService;
use App\Validator;
use App\Helpers;
SessionHelper::start();

$userId = SessionHelper::currentUserId();
if ($userId === null) {
    header('Location: /index.php');
    exit;
}

$user = AuthService::getById($userId);
if ($user === null) {
    SessionHelper::logout();
    header('Location: /index.php');
    exit;
}

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if ($name === '') {
        $errors[] = 'Имя обязательно для заполнения.';
    }
    if (!Validator::validatePhone($phone)) {
        $errors[] = 'Введите корректный телефон.';
    }
    if (!Validator::validateEmail($email)) {
        $errors[] = 'Введите корректный email.';
    }
    if ($password !== '' && mb_strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать не менее 6 символов.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Пароли не совпадают.';
    }

    if (!$errors) {
        try {
            $newPassword = ($password !== '') ? $password : null;
            AuthService::updateProfile(
                $user->id,
                $name,
                $phone,
                $email,
                $newPassword
            );
            $success = true;
            $user = AuthService::getById($user->id);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="container">
    <h1>Профиль</h1>

    <?php if ($success): ?>
        <div class="success">Данные обновлены.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="errors">
            <?php foreach ($errors as $err): ?>
                <p><?= Helpers::e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Имя
            <input type="text" name="name" value="<?= Helpers::e($user->name) ?>" required>
        </label>
        <label>Телефон
            <input type="tel" name="phone" value="<?= Helpers::e($user->phone) ?>" required>
        </label>
        <label>Email
            <input type="email" name="email" value="<?= Helpers::e($user->email) ?>" required>
        </label>
        <fieldset>
            <legend>Смена пароля (оставьте пустым, если не меняете)</legend>
            <label>Новый пароль
                <input type="password" name="password">
            </label>
            <label>Повтор пароля
                <input type="password" name="password_confirm">
            </label>
        </fieldset>
        <button class="btn" type="submit">Сохранить</button>
    </form>
    <p><a href="/index.php">На главную</a> | <a href="/logout.php">Выйти</a></p>
</div>
</body>
</html>
