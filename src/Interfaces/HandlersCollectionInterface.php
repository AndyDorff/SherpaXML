<?php


namespace AndyDorff\SherpaXML\Interfaces;


use AndyDorff\SherpaXML\Handler\AbstractHandler;

interface HandlersCollectionInterface extends \Countable
{
    /**
     * @return AbstractHandler[]
     */
    public function all(): array;
    public function set(string $key, AbstractHandler $handler): void;
    public function get(string $key): ?AbstractHandler;
    public function remove(string $key): void;

    public function equals(HandlersCollectionInterface $handlers): bool;
}