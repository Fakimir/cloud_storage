<?php
namespace Controllers\Classes;
use Core\Interfaces\AppInterface;

class DirectoryController
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    public function get(array $args): array
    {

        try {
            $id = $args['dir_id'];
            $user_id = $args['user_id'];

            $row = $this->app->getRepository('DirectoryRepository')->getById($id, $user_id);
            return [
                'data' => json_encode($row),
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
            $name = $args['name'];
            $user_id = $args['user_id'];

            $this->app->getRepository('DirectoryRepository')->edit($id, $name, $user_id);

            return [
                'data' => json_encode('directory successfully edited'),
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
        $name = $args['name'];
        $user_id = $args['user_id'];
        $this->app->getRepository('DirectoryRepository')->post($name, $user_id);
        return [
            'data' => json_encode('directory successfully added'),
            'headers' => [
                'Content-Type: application/json',
            ]
        ];
    }

    public function delete(array $args): array
    {
        try {
            $id = $args['dir_id'];
            $user_id = $args['user_id'];
            $access = $this->app->getRepository('CheckAccessibilityRepository')->isAccessibleDir($id, $user_id);
            if ($access) {
                //Удаление файлов папки с сервера
                $this->app->getService('Server')->deleteFromServer($id, 'dir_id');

                $this->app->getRepository('FilesRepository')->deleteFromDir($id); //удаление файлов директории с бд

                //удаление самой директории
                $this->app->getRepository('DirectoryRepository')->delete($id);

                return [
                    'data' => json_encode('directory successfully deleted'),
                    'headers' => [
                        'Content-Type: application/json',
                    ]
                ];
            }
            else {
                return [
                    'data' => $access, //поле access в этом случае содержит обьект ошибки
                    'headers' => [
                        'Content-Type: application/json',
                    ]
                ];
            }
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