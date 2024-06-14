<?php
namespace Controllers\Classes;
use Core\Interfaces\AppInterface;

class AdminController
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }
    public function getAll(): array
    {
        try {
            $rows = $this->app->getRepository('AdminRepository')->getAll();
            return [
                'data' => json_encode($rows),
                'headers' => [
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

    public function get(array $args): array
    {
        try {
            $id = $args['id'];
            $user = $this->app->getRepository('AdminRepository')->get($id);
            return [
                'data' => json_encode($user),
                'headers' => [
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

    public function delete(array $args): array
    {
        try {
            $id = $args['id'];
            $this->app->getRepository('AdminRepository')->delete($id);
            return [
                'data' => json_encode('Пользователь успешно удален'),
                'headers' => [
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

    public function edit(array $args): array
    {
        try {
            $id = $args['id'];
            $email = $args['email'];
            $password = $args['password'];
            $role = $args['role'];

            $this->app->getRepository('AdminRepository')->edit($id, $email, $password, $role);
            return [
                'data' => json_encode('Пользователь успешно обновлен'),
                'headers' => [
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