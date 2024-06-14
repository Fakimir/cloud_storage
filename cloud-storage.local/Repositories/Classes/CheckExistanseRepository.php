<?php
namespace Repositories\Classes;

class CheckExistanseRepository
{
    private $connection;

    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function exists(int $id, string $table): bool //проверка на существование поля в бд

    {
        $statement = $this->connection->prepare("SELECT COUNT(*) FROM $table WHERE id = '$id'");
        $statement->execute();
        $result = $statement->fetch();
        if ($result[0] == 0) { //если ничего не было найдено, вызвать ошибку в зависимости от таблицы
            if ($table == 'files') {
                throw new \ErrorException('Such file was not found', 404);
            }
            else if ($table == 'directories') {
                throw new \ErrorException('Such directory was not found', 404);
            }
            return false;
        }
        else {
            return true;
        }
    }
}