<?php
namespace Services\Classes;
use Core\Interfaces\ResponseInterface;

class CreateResponse //вспомогательный класс создания ответа, чтобы не делать это в самом роутере
{
    private $response;
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
    public function createResponse(array $headers, string $data): \Core\Classes\Response
    {
        $this->response->setHeaders($headers);
        $this->response->setData($data);

        return $this->response;
    }
}