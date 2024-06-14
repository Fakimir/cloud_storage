<?php
namespace Repositories\Classes;
use Core\Interfaces\AppInterface;

class CheckAccessibilityRepository
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    public function isAccessibleFile(int $id, int $user_id): bool
    { //проверка есть ли доступ к файлу
        $accessibleFiles = $this->app->getRepository('GetAccessibleFilesRepository')->getAccessibleFiles($user_id);
        $access = false; //переменная отражающая можно ли этому пользователю удалить этот файл
        $exists = $this->app->getRepository('CheckExistanseRepository')->exists($id, 'files'); //переменная отражающая существует ли такой файл
        foreach ($accessibleFiles as $file) {
            if ($file['id'] == $id && $file['user_id'] == $user_id) {
                $access = true;
            }
        }
        if (!$access && $exists) {
            throw new \ErrorException('You have no access to this file', 403);
        }
        return $access;
    }

    public function isAccessibleDir(int $id, int $user_id): bool //проверка есть ли доступ к директории

    {
        $this->app->getRepository('CheckExistanseRepository')->exists($id, 'directories');
        $this->app->getRepository('getAccessibleDirectories')->getAccessibleDirs($id, $user_id);

        return true;
    }
}