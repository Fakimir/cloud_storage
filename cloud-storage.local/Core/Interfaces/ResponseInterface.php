<?php
namespace Core\Interfaces;

interface ResponseInterface
{
    public static function setData(string $data);
    public static function getData();
    public static function setHeaders(array $headers);
    public static function getHeaders();
}