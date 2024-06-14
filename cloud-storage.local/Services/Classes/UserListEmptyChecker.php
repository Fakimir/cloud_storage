<?php
namespace Services\Classes;

class UserListEmptyChecker
{
    public function checkListEmpty(array $arr): void //проверка пустой ли список пользователь, если пусто то скорее всего какая то проблема

    {
        if (empty($arr)) {
            throw new \Exception('Something went wrong', 501);
        }
    }
}