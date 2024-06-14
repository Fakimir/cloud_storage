<?php
namespace Services\Classes;
use Firebase\JWT\JWT;
use Firebase\JWT\KEY;

class JWTAccessTokenChecker
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function checkAccessToken(string $token): bool
    {
        if ($token) {
            // логика проверки токена
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'), array('HS256'));

            return true;
        }
        else {
            throw new \Exception('You havent been authorized', 401);
        }
    }
}