<?php
namespace Repositories\Classes;

class DirectoryRepository

{
    public function __construct(\Core\Interfaces\AppInterface $app)
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
        $this->app = $app;
    }
    public function getById(int $id, int $user_id): string
    {
        $this->app->getRepository('CheckAccessibilityRepository')->isAccessibleDir($id, $user_id);

        return $this->db->find($id);
    }

    public function edit(int $id, string $name, int $user_id): void
    {
        $this->app->getRepository('CheckAccessibilityRepository')->isAccessibleDir($id, $user_id);

        $statement = $this->connection->prepare("UPDATE directories SET name= :name WHERE id='$id'"); //смена имени у дериктории
        $statement->bindValue('name', $name);
        $statement->execute();
    }

    public function post(string $name, int $user_id): void
    {
        $statement = $this->connection->prepare("INSERT INTO directories (
            id,
            name,
            user_id)
      
            VALUES (null, :name, :user_id)
          ");
        $statement->bindValue('name', $name);
        $statement->bindValue('user_id', $user_id);
        $statement->execute(); //Добавление в бд
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM directories WHERE id='$id'"); //Удаление нужной директории
        $statement->execute();
    }
}