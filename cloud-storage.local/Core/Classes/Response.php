<?php
namespace Core\Classes;
use Core\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    private static $data;
    private static $headers;

    public static function setData(string $data)
    {
        self::$data = $data;
    }

    public static function getData()
    {
        if (isset(self::$data)) {
            return self::$data;
        }
        else {
            return 'no data';
        }
    }

    public static function setHeaders(array $headers): void
    {
        self::$headers = $headers;
    }

    public static function getHeaders()
    {
        if (isset(self::$headers)) {
            return self::$headers;
        }
        else {
            return null;
        }
    }
}