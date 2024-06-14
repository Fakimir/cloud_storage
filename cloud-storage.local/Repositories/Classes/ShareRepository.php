<?php
namespace Repositories\Classes;

class ShareRepository
{
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function fileGetShare(int $file_id): array //Получить список связей

    {
        $statement = $this->connection->prepare("SELECT u.*
        FROM users u
        JOIN user_access_files f ON u.id = f.user_id
        WHERE f.file_id = $file_id;");
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }

    public function filePutShare(int $user_id, int $file_id): void //добавить доступ к файлу пользователю

    {
        $statement = $this->connection->prepare("INSERT INTO user_access_files (file_id, user_id) VALUES ($file_id, $user_id);");
        $statement->execute();
    }

    public function fileDeleteShare(int $user_id, int $file_id): void //удалить доступ

    {
        $statement = $this->connection->prepare("DELETE FROM user_access_files WHERE file_id = $file_id AND user_id = $user_id;");
        $statement->execute();
    }
}