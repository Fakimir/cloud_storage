<?php
namespace Core\Classes\FindById;
use Core\Interfaces\FindInterface;

class FindFile implements FindInterface
{
    private $connection;

    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function find(int $id): string
    {
        $statement = $this->connection->prepare("SELECT * FROM files WHERE id='$id'"); //Выбор нужного файла
        $statement->execute();
        header('Content-Type: application/json');
        $row = $statement->fetch();
        return json_encode($row);
    }
}