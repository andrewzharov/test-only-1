<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\SessionHelper;
use App\AuthService;
use App\Validator;
use App\Helpers;
SessionHelper::start();
//Если пользователь залогинен, то редирект на страницу профиля
if (SessionHelper::isLoggedIn()) {
    header('Location: /profile.php');
    exit;
}
//Массив для выводимых ошибок
$errors = [];
//Введенные ранее значения полей
$inputed = ['name' => '', 'phone' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    $inputed = ['name' => $name, 'phone' => $phone, 'email' => $email];

    if ($name === '') {
        $errors[] = 'Имя обязательно для заполнения.';
    }
    if (!Validator::validatePhone($phone)) {
        $errors[] = 'Введите корректный телефон (10–15 цифр).';
    }
    if (!Validator::validateEmail($email)) {
        $errors[] = 'Введите корректный email.';
    }
    if (mb_strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать не менее 6 символов.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Пароли не совпадают.';
    }
    //если ошибок нет, то мы проводим регистрацию пользователя, логиним его и редиректим на страницу профиля
    if (!$errors) {
        try {
            $user = AuthService::register($name, $phone, $email, $password);
            SessionHelper::loginUser($user);
            header('Location: /profile.php');
            exit;
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
    <title>Регистрация</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="container">
    <h1>Регистрация</h1>

    <?php if ($errors): ?>
        <div class="errors">
            <?php foreach ($errors as $err): ?>
                <p><?= Helpers::e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Имя
            <input type="text" name="name" value="<?= Helpers::e($inputed['name']) ?>" required>
        </label>
        <label>Телефон
            <input type="tel" name="phone" value="<?= Helpers::e($inputed['phone']) ?>" required>
        </label>
        <label>Email
            <input type="email" name="email" value="<?= Helpers::e($inputed['email']) ?>" required>
        </label>
        <label>Пароль
            <input type="password" name="password" required>
        </label>
        <label>Повтор пароля
            <input type="password" name="password_confirm" required>
        </label>
        <button class="btn" type="submit">Зарегистрироваться</button>
    </form>
    <p><a href="/login.php">Уже есть аккаунт? Войти</a></p>
</div>
</body>
</html>