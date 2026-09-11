<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\SessionHelper;
use App\AuthService;
use App\CaptchaService;
use App\Config;
use App\Helpers;

SessionHelper::start();

if (SessionHelper::isLoggedIn()) {
    header('Location: /profile.php');
    exit;
}

$errors = [];
$oldLogin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $token    = $_POST['smart-token'] ?? '';

    $oldLogin = $login;

    if ($token === '' || !CaptchaService::verify($token)) {
        $errors[] = 'Подтвердите, что вы не робот.';
    }

    if (!$errors) {
        $user = AuthService::login($login, $password);
        if ($user) {
            SessionHelper::loginUser($user);
            header('Location: /profile.php');
            exit;
        }
        $errors[] = 'Неверный логин или пароль.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://smartcaptcha.cloud.yandex.ru/captcha.js" defer></script>
</head>
<body>
<div class="container">
    <h1>Авторизация</h1>

    <?php if ($errors): ?>
        <div class="errors">
            <?php foreach ($errors as $err): ?>
                <p><?= Helpers::e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" id="login-form">
        <label>Телефон или Email
            <input type="text" name="login" value="<?= Helpers::e($oldLogin) ?>" required>
        </label>
        <label>Пароль
            <input type="password" name="password" required>
        </label>

        <div id="captcha-container" class="smart-captcha"
             data-sitekey="<?= Helpers::e(Config::smartcaptchaClientKey()) ?>">
        </div>

        <button class="btn" type="submit">Войти</button>
    </form>
    <p><a href="/register.php">Нет аккаунта? Зарегистрироваться</a></p>
</div>
</body>
</html>
