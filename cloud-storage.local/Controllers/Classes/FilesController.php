<?php
namespace Controllers\Classes;
use Core\Interfaces\AppInterface;

class FilesController
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }
    public function get(array $args): array
    {
        try {
            $id = $args['id'];
            $user_id = $args['user_id'];
            $file = $this->app->getRepository('FilesRepository')->getById($id, $user_id);
            return [
                'data' => json_encode($file),
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
            $id = $args['user_id'];

            $rows = $this->app->getRepository('GetAccessibleFilesRepository')->getAccessibleFiles($id);
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

    public function post(array $args): array
    {
        try {
            $user_id = $args['user_id'];
            $name = $args['name'];
            $type = $args['type'];
            $size = $args['size'];
            $dir_id = $args['dir_id'];
            $error = $args['error'];

            $filename_parts = explode('.', $name);
            $file_extension = end($filename_parts);
            $file_name = reset($filename_parts);
            $encodedName = base64_encode($file_name . uniqid());
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/files/';
            $target_file = $target_dir . $encodedName . '.' . $file_extension;

            //encoded name для бд
            $encoded_name = $encodedName . '.' . $file_extension;

            if ($error > 0) {
                $text = "Uploading error: " . $error;
                throw new \ErrorException($text, 500);
            }
            else {
                if ($this->app->getRepository('CheckAccessibilityRepository')->isAccessibleDir($dir_id, $user_id)) {
                    $this->app->getRepository('FilesRepository')->post($user_id, $name, $encoded_name, $dir_id, $type, $size); //добавление на бд

                    $this->app->getService('AddFileToServer')->addFileToServer($target_file); //добавление на сервер

                    return [
                        'data' => json_encode('File successfully uploaded'),
                        'headers' => [
                            'Content-Type: application/json',
                        ]
                    ];
                }
                else {
                    throw new \ErrorException('No such directory or you have no access to it', 500);
                }
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

    public function delete(array $args): array
    {
        try {
            $id = $args['id'];
            $user_id = $args['user_id'];

            if ($this->app->getRepository('CheckAccessibilityRepository')->isAccessibleFile($id, $user_id)) {
                //удаление с сервера
                $this->app->getService('Server')->deleteFromServer($id, 'id');

                //удаление с бд
                $this->app->getRepository('FilesRepository')->delete($id);
            }

            return [
                'data' => json_encode('File was successfully deleted'),
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
            //получаем аргументы
            $id = $args['id'];
            $name = $args['name'];
            $dir_id = $args['dir_id'];
            $user_id = $args['user_id'];

            $access = $this->app->getRepository('CheckAccessibilityRepository')->isAccessibleDir($dir_id, $user_id) && $this->app->getRepository('CheckAccessibilityRepository')->isAccessibleFile($id, $user_id);

            if ($access) {
                $encoded_name = $this->app->getRepository('FilesRepository')->getEncodedName($id);

                $filename_parts = explode('.', $encoded_name);
                $file_extension = end($filename_parts);
                $new_encoded_name = base64_encode($name . uniqid()) . '.' . $file_extension;

                $this->app->getRepository('FilesRepository')->edit($name, $new_encoded_name, $dir_id, $id); //изменение в бд

                $this->app->getService('Server')->editInServer($name, $encoded_name, $new_encoded_name); //изменение на сервере

                return [
                    'data' => json_encode('File was successfully updated'),
                    'headers' => [
                        'Content-Type: application/json',
                    ]
                ];
            }
            else {
                return [
                    'data' => $access, //поле access будет содержать обьект ошибкт в данном случае
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