<?php

declare(strict_types=1);

namespace App;

final class CaptchaService
{
    public static function verify(string $token): bool
    {
        $secret = Config::smartcaptchaSecretKey();

        $ch = curl_init('https://smartcaptcha.cloud.yandex.ru/validate');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS    => http_build_query([
                'secret' => $secret,
                'token'  => $token,
                'ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
            ]),
            CURLOPT_TIMEOUT        => 1,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Если сервис капчи не отвечает, то пропускаем пользователя
        if ($httpCode !== 200) {
            return true;
        }

        $data = json_decode($response, true);
        return ($data['status'] ?? '') === 'ok';
    }
}
