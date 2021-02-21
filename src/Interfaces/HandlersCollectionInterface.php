<?php


namespace AndyDorff\SherpaXML\Interfaces;


use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;

interface HandlersCollectionInterface extends \Countable
{
    /**
     * @return Handler[]
     */
    public function all(): array;
    public function put(Handler $handler): void;
    public function get(HandlerId $handlerId): ?Handler;
    public function remove(HandlerId $handlerId): void;
    public function replicate(): HandlersCollectionInterface;

    public function equals(HandlersCollectionInterface $handlers): bool;
}