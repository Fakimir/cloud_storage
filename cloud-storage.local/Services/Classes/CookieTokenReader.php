<?php
namespace Services\Classes;

class CookieTokenReader //получение именно из куки, еще я добавлял второй вариант через header
{
    public function getToken(): string
    {
        if (isset($_COOKIE['access_token'])) {
            return $_COOKIE['access_token'];
        }
        else {
            throw new \Exception('Invalid cookie');
        }
    }
}