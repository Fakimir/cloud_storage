<?php
namespace Core\Classes;
use Core\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    public function __construct($url, $method)
    {
        $this->url = $url;
        $this->method = $method;
    }
    public function getData(): array
    {
        $method = $this->method;
        $requestUri = $this->url;
        $isAdmin = preg_match('/^\/admin\/user\/(\d+)$/', $requestUri) == 1;

        if ($method == 'GET' || $method == 'DELETE') {
            if (preg_match('/^\/file\/(\d+)$/', $requestUri) == 1) {
                $args['id'] = explode('/', $_SERVER['REQUEST_URI'])[2];
            }
            else if ((preg_match('/^\/files\/share\/\d+\/\d+$/', $requestUri) == 1) && $method == 'DELETE') {
                $args['access_user_id'] = explode('/', $_SERVER['REQUEST_URI'])[4];
                $args['file_id'] = explode('/', $_SERVER['REQUEST_URI'])[3];
            }
            else if (($isAdmin) || (preg_match('/^\/files\/share\/\d+$/', $requestUri) == 1)) {
                $args['id'] = explode('/', $_SERVER['REQUEST_URI'])[3];
            }
            else if (preg_match('/^\/directory\/(\d+)$/', $requestUri) == 1) {
                $args['dir_id'] = explode('/', $_SERVER['REQUEST_URI'])[2];
            }
            else if ($requestUri !== '/login' && $requestUri !== '/' && $requestUri !== '/user/') {
                $args['id'] = explode('/', $_SERVER['REQUEST_URI'])[2];
            }
            else {
                $args = $_GET;
            }
        }
        else if ($method == 'PUT') { //добавление параметров к put запросам
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            //тут мы собираем общие параметры для всех запросов put, чтобы не делать обработку для каждого эндпоинта
            $args = [
                'id' => $data['id'] ?? $_GET['id'] ?? null,
                'name' => $data['name'] ?? $_GET['name'] ?? null,
                'dir_id' => $data['dir_id'] ?? $_GET['dir_id'] ?? null,
                'email' => $data['email'] ?? $_GET['email'] ?? null,
                'password' => $data['password'] ?? $_GET['password'] ?? null,
                'role' => $data['role'] ?? $_GET['role'] ?? null,
            ];
        }
        else if ($method == 'POST') { //добавление аргументов для post запросов
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            $args = [ //тут аналогично как и с запросом put
                'name' => $data['name'] ?? $_POST['name'] ?? null,
                'email' => $data['email'] ?? $_POST['email'] ?? null,
                'password' => $data['password'] ?? $_POST['password'] ?? null,
                'dir_id' => $data['dir_id'] ?? $_POST['dir_id'] ?? null,
                'file' => $data['file'] ?? $_FILES ?? null,
                'access_user_id' => explode('/', $_SERVER['REQUEST_URI'])[4] ?? null,
                'file_id' => explode('/', $_SERVER['REQUEST_URI'])[3] ?? null,
                'role' => $data['role'] ?? $_GET['role'] ?? null,
            ];

            if (isset($args['file']) && !empty($args['file'])) {
                $file = current($args['file']); // получение информации о файле
                $args['name'] = $file['name'];
                $args['type'] = $file['type'];
                $args['size'] = $file['size'];
                $args['error'] = $file['error'];
            }
        }

        return $args;
    }

    public function getRoute(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

}