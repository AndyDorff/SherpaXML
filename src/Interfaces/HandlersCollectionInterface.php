<?php


namespace AndyDorff\SherpaXML\Interfaces;



use AndyDorff\SherpaXML\Handler\AbstractClosureHandler;

interface HandlersCollectionInterface extends \Countable
{
    /**
     * @return AbstractClosureHandler[]
     */
    public function all(): array;
    public function set(string $key, AbstractClosureHandler $handler): void;
    public function get(string $key): ?AbstractClosureHandler;
    public function remove(string $key): void;

    public function equals(HandlersCollectionInterface $handlers): bool;
}