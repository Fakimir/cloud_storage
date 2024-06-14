<?php
namespace Repositories\Classes;

class getAccessibleDirectories
{
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function getAccessibleDirs(int $dir_id, int $user_id): bool
    {
        $statement = $this->connection->prepare("
        SELECT id
        FROM directories
        WHERE id = :dir_id AND user_id = :user_id
        ");
        $statement->bindValue("dir_id", $dir_id);
        $statement->bindValue("user_id", $user_id);
        $statement->execute();
        $dir = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$dir) {
            throw new \ErrorException('You have no access to this directory', 403); //вернет ошибку если не найдет файл
        }
        else {
            return true;
        }
    }
}