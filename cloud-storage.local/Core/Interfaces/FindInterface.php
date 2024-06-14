<?php
namespace Core\Interfaces;

interface FindInterface
{
    public function find(int $id): string;
}