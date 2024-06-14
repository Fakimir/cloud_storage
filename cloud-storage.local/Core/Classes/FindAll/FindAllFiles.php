<?php
namespace Core\Classes\FindAll;
use Core\Interfaces\FindAllInterface;

class FindAllFiles implements FindAllInterface
{
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function findAll(array $args): array
    {
        $id = $args['id'];
        $statement = $this->connection->prepare("(
            SELECT files.*
            FROM files
            WHERE files.user_id = :id
        )
        UNION
        (
            SELECT files.*
            FROM files
            JOIN user_access_files ON files.id = user_access_files.file_id
            WHERE user_access_files.user_id = :id
        )"); //Выбор тех файлов, который сохранены пользователем + те файлы к которым мы имеем доступ
        $statement->bindValue("id", $id);
        $statement->execute();
        header('Content-Type: application/json');
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}