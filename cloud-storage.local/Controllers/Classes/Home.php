<?php
namespace Controllers\Classes;
use Controllers\Interfaces\HomeInterface;

class Home
{
    public function list(): array
    {
        return [
            'data' => 'Welcome',
            'headers' => [
                'Content-Type: application/json',
            ]
        ];
    }
}