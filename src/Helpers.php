<?php

declare(strict_types=1);

namespace App;

class Helpers{
    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}