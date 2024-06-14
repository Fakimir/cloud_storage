<?php
namespace Repositories\Classes;

class FilesRepository
{
    public function __construct(\Core\Interfaces\AppInterface $app)
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
        $this->app = $app;
    }

    public function getById(int $id, int $user_id): string
    {
        $this->app->getRepository('CheckAccessibilityRepository')->isAccessibleFile($id, $user_id); //проверка есть ли такой файл и доступен ли он, если нет просто выкенет ошибку
        return $this->db->find($id);
    }

    public function post(int $user_id, string $name, string $encoded_name, int $dir_id, string $type, int $size): void
    {
        $statement = $this->connection->prepare("INSERT INTO files (
            id,
            user_id,
            name,
            encoded_name,
            type,
            size,
            dir_id)
      
            VALUES (null, :user_id, :name, :encoded_name, :type, :size, :dir_id)
          ");

        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('name', $name);
        $statement->bindValue('encoded_name', $encoded_name);
        $statement->bindValue('dir_id', $dir_id);
        $statement->bindValue('type', $type);
        $statement->bindValue('size', $size);
        $statement->execute(); //Добавление в бд
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM files WHERE id='$id'");
        $statement->execute();
    }

    public function getEncodedName(int $id): string //получить закодированное имя для действий на сервере

    {
        //получение данных о файлах
        $statement = $this->connection->prepare("SELECT encoded_name FROM files WHERE id='$id'"); //Выбор нужного файла
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        $encoded_name = $row['encoded_name'];

        return $encoded_name;
    }

    public function edit(string $name, string $new_encoded_name, int $dir_id, int $id): void
    {
        //переименование или перемещение в бд
        $statement = $this->connection->prepare("UPDATE files SET name= :name, encoded_name= :encoded_name, dir_id= :dir_id WHERE id='$id'");
        $statement->bindValue('name', $name);
        $statement->bindValue('encoded_name', $new_encoded_name);
        $statement->bindValue('dir_id', $dir_id);
        $statement->execute();
    }

    public function deleteFromDir(int $id): void
    {
        //удаление файлов с бд
        $statement = $this->connection->prepare("DELETE FROM files WHERE dir_id='$id'");
        $statement->execute();
    }

    public function getFilesFromDir(string $elem, int $id): array
    {
        $statement = $this->connection->prepare("SELECT name, encoded_name FROM files WHERE $elem=:elem");
        $statement->bindParam(":elem", $id);
        $statement->execute();
        $files = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $files;
    }
}