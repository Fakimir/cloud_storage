<?php
namespace Services\Classes;

class AuthorizationHeaderReader
{
    public function getToken(): string //альтернативная версия получения токена из хедеров

    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            return $headers['Authorization'];
        }
        else {
            throw new \Exception('Invalid authorization header');
        }
    }
}