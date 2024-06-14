<?php
namespace Services\Classes;
use Firebase\JWT\JWT;
use Firebase\JWT\KEY;

class TokenDataExtractor
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }
    public function getDataFromToken(string $token): array //получить данные из токена

    {
        $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'), array('HS256'));
        $role = $decoded->role;
        $userId = $decoded->id;

        return [
            'role' => $role,
            'user_id' => $userId,
        ];
    }
}