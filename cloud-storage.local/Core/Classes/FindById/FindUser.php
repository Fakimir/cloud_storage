<?php
namespace Core\Classes\FindById;
use Core\Interfaces\FindInterface;

class FindUser implements FindInterface
{
    private $connection;
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function find(int $id): string
    {
        $statement = $this->connection->prepare("SELECT * FROM users WHERE id=:id");
        $statement->bindParam(':id', $id);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return json_encode($row);
    }
}