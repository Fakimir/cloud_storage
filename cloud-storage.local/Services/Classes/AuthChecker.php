<?php
namespace Services\Classes;
class AuthChecker
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function authorize(): array
    {
        try {
            $token = $this->auth->getToken();
            $accessToken = $this->auth->checkAccessToken($token);

            if (is_array($accessToken) && isset($accessToken['headers'])) {
                // Обработка перенаправления
                return [
                    'access' => true,
                    'data' => 'redirect',
                ];
            }

            // Если авторизация прошла успешно
            return [
                'access' => true,
                'data' => $this->auth->getDataFromToken($token),
            ];
        }
        catch (\Exception $e) { //ошибка проверки токена, тоесть он не действителен
            return [
                'access' => false,
                'data' => $e,
            ];
        }
    }
}