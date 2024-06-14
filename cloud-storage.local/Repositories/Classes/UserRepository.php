<?php
namespace Repositories\Classes;

class UserRepository
{
    private $connection;

    public function __construct(\Core\Interfaces\AppInterface $app)
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
        $this->app = $app;
    }

    public function getById(int $id): string
    {
        $this->isUserExists($id);
        return $this->db->find($id);
    }

    public function getAll(): array
    {
        $rows = $this->db->findAll([]);
        $this->app->getService('UserListEmptyChecker')->checkListEmpty($rows);
        return $rows;
    }

    public function post(string $email, string $password, string $role): void
    {
        $statement = $this->connection->prepare("INSERT INTO users (
            id,
            email,
            password,
            role)
      
            VALUES (null, :email, :password, :role)
          ");

        $statement->bindValue('email', $email);
        $statement->bindValue('password', password_hash($password, PASSWORD_DEFAULT));
        $statement->bindValue('role', $role);
        $statement->execute();
    }

    public function edit(int $id, string $email, string $password, string $role): void
    {
        $this->isUserExists($id);
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $statement = $this->connection->prepare("UPDATE users SET email = '$email', password = '$hashPassword', role='$role' WHERE id = '$id'");
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $this->isUserExists($id);
        $statement = $this->connection->prepare("DELETE FROM users WHERE id='$id'");
        $statement->execute();
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