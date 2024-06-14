<?php
namespace Core\Classes\FindById;
use Core\Interfaces\FindInterface;

class FindDirectory implements FindInterface
{
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }

    public function find(int $id): string
    {
        $statement = $this->connection->prepare("SELECT * FROM files WHERE dir_id='$id'"); //Выбор нужных файлов
        $statement->execute();
        header('Content-Type: application/json');
        $row = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return json_encode($row);
    }
}