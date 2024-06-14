<?php
namespace Repositories\Classes;

class AdminRepository
{
    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }

    public function getAll(): array
    {
        $rows = $this->db->findAll([]);
        return $rows;
    }

    public function get(int $id): string
    {
        $this->isUserExists($id); //проверка существует ли пользователь
        return $this->db->find($id);
    }

    public function delete(int $id): void
    {
        $isUserExist = $this->isUserExists($id);
        if ($isUserExist) {
            $statement = $this->connection->prepare("DELETE FROM users WHERE id='$id'");
            $statement->execute();
        }
    }

    public function edit(int $id, string $email, string $password, string $role): void
    {
        $isUserExist = $this->isUserExists($id);
        if ($isUserExist == true) {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $statement = $this->connection->prepare("UPDATE users SET email = '$email', password = '$hashPassword', role='$role' WHERE id = '$id'");
            $statement->execute();
        }
    }

    public function isUserExists(int $id): bool //проверка существует ли пользователь

    {
        $statement = $this->connection->prepare("SELECT COUNT(*) FROM users WHERE id = '$id'");
        $statement->execute();
        $result = $statement->fetch();
        if ($result[0] == 0) {
            throw new \Exception('There is no such user in the database', 404);
        }
        else {
            return true;
        }
    }
}