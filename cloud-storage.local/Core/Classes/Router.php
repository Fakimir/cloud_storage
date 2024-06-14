<?php
namespace Core\Classes;
use Core\Interfaces\RouterInterface;
use Core\Interfaces\AppInterface;

class Router implements RouterInterface
{
    private $urlList;
    private $regexKeys;

    public function __construct(AppInterface $app, $urlList, $regexKeys)
    {
        $this->app = $app;
        $this->urlList = $urlList;
        $this->regexKeys = $regexKeys;
    }

    public function processRequest(\Core\Interfaces\RequestInterface $request): Response //передача request и получение ответа

    {
        try {
            $handler = $this->findHandler($request->getRoute(), $request->getMethod());
            $authChecker = $this->app->getService('AuthChecker')->authorize();

            $controllerName = $handler['controller'];
            $controllerMethod = $handler['method'];

            $controller = $this->app->getController($controllerName);

            $args = $request->getData();

            if (($authChecker['data'] instanceof \Exception == false && $authChecker['access'] === true) || $controllerName == 'AuthController') { // Эта проверка нужна чтобы создать респонс с ошибкой если не пройдена авторизация, или наоборот пропустить запрос авторизации если метод login

                if ($controllerName !== 'AuthController' || $controllerMethod !== 'login') { //тут такая проверка, что если идет метод авторизации то полей user_id и role не будет в $args поэтому сделано чтоб не было ошибки
                    $args['user_id'] = $authChecker['data']['user_id'];
                    $args['role'] = $authChecker['data']['role'];

                    if ($controllerName == 'AdminController') { //проверка на админа
                        $this->app->getService('AdminService')->isAdmin($args['role']);
                    }
                }

                $responseData = $controller->$controllerMethod($args); // вызоа контроллера и получение data

                $data = $responseData['data'];
                $headers = $responseData['headers'];


                $response = $this->app->getService('CreateResponse')->createResponse($headers, $data);

                return $response;
            }
            else {
                $headers = [
                    'Content-Type: application/json',
                ];

                $response = $this->app->getService('CreateResponse')->createResponse($headers, $authChecker);

                return $response;
            }
        }
        catch (\Exception $e) {
            $headers = [
                'Content-Type: application/json',
            ];

            $response = $this->app->getService('CreateResponse')->createResponse($headers, $e);

            return $response;
        }
    }

    private function findHandler(string $url, string $method): array //вспомогательный метод, который ищет нужный контроллер и его метод

    {
        foreach ($this->urlList as $pattern => $handlers) {
            if ($this->urlMatchesPattern($url, $pattern)) {
                if (array_key_exists($method, $handlers)) {
                    return [
                        'controller' => explode('::', $handlers[$method])[0],
                        'method' => explode('::', $handlers[$method])[1]
                    ];
                }
                else {
                    throw new \Exception('Method isn\'t supported', 501); //если нету такого метода, то выдать ошибку
                }
            }
        }

        throw new \Exception('URL not found', 404); //и в случае если нет url тоже выдать ошибку
    }
    private function urlMatchesPattern(string $url, string $pattern): bool //проверка есть ли соответствия url и regex кода. Тоесть это надо когда url динамический 

    {
        return $url === $pattern || ($this->isRegexPattern($pattern) && preg_match($pattern, $url));
    }

    private function isRegexPattern(string $pattern): bool //проверка есть ли url в списке со всеми regex кодами, важно, что в этот список надо добавить url если он динамический иначе будет ошибка

    {
        return in_array($pattern, $this->regexKeys);
    }
}