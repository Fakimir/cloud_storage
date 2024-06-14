<?php

function autoload(string $className, string $prefix)
{
    // корень
    $baseDir = __DIR__ . '/../';

    // Длина префикса пространства имен
    $prefixLength = strlen($prefix);

    if (strncmp($prefix, $className, $prefixLength) !== 0) {
        return;
    }

    // Относительное имя класса без префикса
    $relativeClassName = substr($className, $prefixLength);

    $pathFromPrefix = str_replace('\\', '/', $prefix);

    // путь к файлу
    $filePath = $baseDir . $pathFromPrefix . str_replace('\\', '/', $relativeClassName) . '.php';

    // подключение
    if (file_exists($filePath)) {
        require $filePath;
    }
}

spl_autoload_register(function (string $className) { //интерфейсы Core
    autoload($className, 'Core\\Interfaces\\');
});

spl_autoload_register(function (string $className) { //Классы из Core
    autoload($className, 'Core\\Classes\\');
});

spl_autoload_register(function (string $className) { //сервисы
    autoload($className, 'Services\\Classes\\');
});

spl_autoload_register(function (string $className) { //репозитории
    autoload($className, 'Repositories\\Classes\\');
});

spl_autoload_register(function (string $className) { //контроллеры
    autoload($className, 'Controllers\\Classes\\');
});