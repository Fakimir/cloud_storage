<?php
namespace Core\Interfaces;

interface RouterInterface
{
    public function processRequest(\Core\Interfaces\RequestInterface $request);
}