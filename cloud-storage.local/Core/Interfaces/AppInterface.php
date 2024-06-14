<?php
namespace Core\Interfaces;

interface AppInterface
{
    public static function registerService(string $serviceName, $service);
    public static function getService(string $serviceName);

    public static function registerRepository(string $repositoryName, $repository);
    public static function getRepository(string $repositoryName);

    public static function getController(string $controllerName);
    public static function registerController(string $controllerName, $controller);
}