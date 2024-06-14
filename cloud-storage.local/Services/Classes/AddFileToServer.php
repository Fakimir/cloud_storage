<?php
namespace Services\Classes;

class AddFileToServer
{
    public function addFileToServer(string $target_file): string //загрузка файла на сервер

    {
        if (move_uploaded_file(current($_FILES)["tmp_name"], $target_file)) {
            return 'Your file successfully uploaded';
        }
        else {
            throw new \ErrorException('Ошибка загрузки файла', 500);
        }
    }
}