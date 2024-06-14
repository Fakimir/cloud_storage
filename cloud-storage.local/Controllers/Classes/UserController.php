<?php
namespace Controllers\Classes;
use Core\Interfaces\AppInterface;

class UserController
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    public function get(array $args): array
    {
        try {
            $id = $args['id'];
            $user = $this->app->getRepository('UserRepository')->getById($id);
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

    public function getAll(array $args): array
    {
        try {
            $users = $this->app->getRepository('UserRepository')->getAll();
            return [
                'data' => json_encode($users),
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

    public function post(array $args): array
    {
        try {
            $email = $args['email'];
            $password = $args['password'];
            $role = $args['role'];

            $this->app->getRepository('UserRepository')->post($email, $password, $role);
            return [
                'data' => 'Пользователь загружен успешно',
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
            $this->app->getRepository('UserRepository')->delete($id);
            return [
                'data' => 'Пользователь успешно удален',
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

            $this->app->getRepository('UserRepository')->edit($id, $email, $password, $role);
            return [
                'data' => 'Пользователь успешно обновлен',
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