<?php
namespace Core\Interfaces;

interface DbInterface
{
    public static function getInstance();
    public function setFindStrategy(FindInterface $findStrategy): void;
    public function setFindAllStrategy(FindAllInterface $findAllStrategy): void;
    public function getConnection(): \PDO;
    public function find(int $id): string;
    public function findAll(array $args): array;
}