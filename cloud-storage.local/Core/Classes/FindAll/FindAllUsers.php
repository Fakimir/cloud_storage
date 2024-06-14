<?php
namespace Core\Classes\FindAll;
use Core\Interfaces\FindAllInterface;

class FindAllUsers implements FindAllInterface
{
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function findAll(array $args): array
    {
        $statement = $this->connection->prepare("SELECT * FROM users");
        $statement->execute();
        header('Content-Type: application/json');
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }
}