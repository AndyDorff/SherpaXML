<?php


namespace AndyDorff\SherpaXML\HandlerCollections;


use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;
use AndyDorff\SherpaXML\Interfaces\HandlersCollectionInterface;

final class InMemoryHandlersCollection implements HandlersCollectionInterface
{
    private array $handlers = [];

    public function __construct(array $handlers = [])
    {
        $this->setHandlers($handlers);
    }

    private function setHandlers(array $handlers): void
    {
        array_walk($handlers, [$this, 'put']);
        reset($handlers);
   }

    public function all(): array
    {
        return $this->handlers;
    }

    public function put(Handler $handler): void
    {
        $this->handlers[strval($handler->id())] = $handler;
    }

    public function get(HandlerId $handlerId): ?Handler
    {
        return $this->offsetGet($handlerId);
    }

    private function offsetGet(string $offset): ?Handler
    {
        return ($this->handlers[$offset] ?? null);
    }

    public function remove(HandlerId $handlerId): void
    {
        // TODO: Implement remove() method.
    }

    public function replicate(): HandlersCollectionInterface
    {
        return new self($this->handlers);
    }

    public function equals(HandlersCollectionInterface $handlers): bool
    {
        foreach($this->all() as $handler){
            if(!$handlers->get($handler->id())){
                return false;
            }
        }

        return $this->count() === $handlers->count();
    }

    public function count()
    {
        return count($this->all());
    }
}