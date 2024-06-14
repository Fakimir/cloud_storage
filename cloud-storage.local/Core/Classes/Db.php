<?php
namespace Core\Classes;
use Core\Interfaces\DbInterface;
use Core\Interfaces\FindInterface;
use Core\Interfaces\FindAllInterface;

class Db implements DbInterface
{
    private static $instance = null;
    private $connection;
    private $findStrategy = null;
    private $findAllStrategy = null;

    private function __construct()
    {
        $config = include __DIR__ . '/../../config.php';
        $dbConfig = $config['db'];
        $this->connection = new \PDO('mysql:host=' . $dbConfig['host'] . ';port=' . $dbConfig['port'] . ';dbname=' . $dbConfig['dbname'], $dbConfig['user'], $dbConfig['password']);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setFindStrategy(FindInterface $findStrategy): void //добавить стратегию для find

    {
        $this->findStrategy = $findStrategy;
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    public function find(int $id): string
    {
        return $this->findStrategy->find($id);
    }

    public function setFindAllStrategy(FindAllInterface $findAllStrategy): void //добавить стратегию для findAll

    {
        $this->findAllStrategy = $findAllStrategy;
    }

    public function findAll(array $args): array
    {
        return $this->findAllStrategy->findAll($args);
    }
}