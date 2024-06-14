<?php
namespace Services\Classes;

class Auth
{
    private $tokenReader;
    private $tokenChecker;
    private $tokenDataExtractor;

    public function __construct(
        CookieTokenReader $tokenReader,
        JWTAccessTokenChecker $tokenChecker,
        TokenDataExtractor $tokenDataExtractor
        )
    {
        $this->tokenReader = $tokenReader;
        $this->tokenChecker = $tokenChecker;
        $this->tokenDataExtractor = $tokenDataExtractor;
    }

    public function getToken(): string
    {
        return $this->tokenReader->getToken();
    }

    public function checkAccessToken(string $token): bool
    {
        return $this->tokenChecker->checkAccessToken($token);
    }

    public function getDataFromToken(string $token): array
    {
        return $this->tokenDataExtractor->getDataFromToken($token);
    }
}