<?php
namespace Services\Classes;
use Core\Interfaces\AppInterface;

class Server
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }
    public function deleteFromServer(int $id, string $elem): void
    {
        $files = $this->app->getRepository('FilesRepository')->getFilesFromDir($elem, $id);

        foreach ($files as $file) {
            $name = $file["encoded_name"]; // декодируем имя файла
            $path = $_SERVER['DOCUMENT_ROOT'] . "/files/$name"; // путь к файлу на сервере
            if (file_exists($path)) {
                unlink($path); // удаляем файл с сервера
            }
        }
    }

    public function editInServer(string $name, string $encoded_name, string $new_encoded_name): bool
    {
        //изменение на сервере
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/files/';
        $oldName = $target_dir . $encoded_name;
        $newName = $target_dir . $new_encoded_name;

        if (rename($oldName, $newName)) {
            return true;
        }
        else {
            throw new \ErrorException('Renaming error', 500);
        }
    }
}