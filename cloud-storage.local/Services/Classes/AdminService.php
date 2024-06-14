<?php
namespace Services\Classes;

class AdminService
{
    public function isAdmin(string $role): void //проверка на админа

    {
        if ($role !== 'admin') {
            throw new \Exception('You are not an admin to open this source');
        }
    }
}