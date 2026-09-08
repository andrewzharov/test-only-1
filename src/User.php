<?php
declare(strict_types=1);
namespace App;

class User
{
    public function __construct()
    {
        public int $id;
        public string $name;
        public string $phone;
        public string $email;
    }

    public function getDisplayName():string
    {
        return $this->name !== '' ? $this->name : $this->email;
    }

    public function hasPhone(): bool
    {
        return $this->phone !== '' && $this->phone !== null;
    }

    public function hasEmail(): bool
    {
        return $this->email !== '' && $this->email !== null;
    }
}