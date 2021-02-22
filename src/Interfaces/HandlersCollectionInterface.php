<?php


namespace AndyDorff\SherpaXML\Interfaces;


use AndyDorff\SherpaXML\Handler\Handler;

interface HandlersCollectionInterface extends \Countable
{
    /**
     * @return Handler[]
     */
    public function all(): array;
    public function set(string $key, Handler $handler): void;
    public function get(string $key): ?Handler;
    public function remove(string $key): void;

    public function equals(HandlersCollectionInterface $handlers): bool;
}