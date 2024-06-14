<?php
namespace Controllers\Classes;
use Core\Interfaces\AppInterface;

class AuthController
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }
    public function login(array $args): array
    {
        try {
            $email = $args['email'];
            $password = $args['password'];
            $data = $this->app->getRepository('AuthRepository')->login($email, $password);
            return [
                'data' => $data['data'],
                'headers' => $data['headers'],
            ];
        }
        catch (\Exception $e) {
            return [
                'data' => $e,
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
    }
    public function logout(): void
    {
        unset($_COOKIE["access_token"]);
        setcookie("access_token", "", time() - 3600, "/", "cloud-storage.local", false, false);
        header("Location: /");
        exit();
    }
    public function resetPassword(array $args): array
    {
        try {
            $password = $args['password'];
            $email = $args['email'];

            $this->app->getRepository('AuthRepository')->resetPassword($email, $password);

            return [
                'data' => json_encode('Changing password procces complete successfully'),
                'headers' => [
                    'Location: /',
                    'Content-Type: application/json',
                ]
            ];
        }
        catch (\Exception $e) {
            return [
                'data' => $e,
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
    }
}