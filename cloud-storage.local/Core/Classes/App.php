<?php
namespace Core\Classes;
use Core\Interfaces\AppInterface;

class App implements AppInterface
{
    private static $services = [];
    private static $repositories = [];
    private static $controllers = [];

    public static function registerService(string $serviceName, $service) //добавить сервис

    {
        self::$services[$serviceName] = $service;
    }

    public static function getService(string $serviceName) //получить сервис

    {
        if (isset(self::$services[$serviceName])) {
            return self::$services[$serviceName];
        }
        else {
            return null;
        }
    }

    public static function registerRepository(string $repositoryName, $repository) //добавить репозиторий

    {
        self::$repositories[$repositoryName] = $repository;
    }

    public static function getRepository(string $repositoryName) //получить репозиторий

    {
        if (isset(self::$repositories[$repositoryName])) {
            return self::$repositories[$repositoryName];
        }
        else {
            return null;
        }
    }

    public static function registerController(string $controllerName, $controller) //добавить контроллер

    {
        self::$controllers[$controllerName] = $controller;
    }

    public static function getController(string $controllerName) //получить контроллер

    {
        if (isset(self::$controllers[$controllerName])) {
            return self::$controllers[$controllerName];
        }
        else {
            return null;
        }
    }
}