<?php
namespace Repositories\Classes;

class GetAccessibleFilesRepository
{
    private $connection;

    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
    }
    public function getAccessibleFiles(int $id): array
    { //Этот метод мне нужен для того чтобы было удобнее проверять есть ли доступ к файлу, мне это понадобится в методе fileDelete и directoryDelete
        return $this->db->findAll(['id' => $id]);
    }
}