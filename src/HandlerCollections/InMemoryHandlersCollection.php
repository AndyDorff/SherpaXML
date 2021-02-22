<?php


namespace AndyDorff\SherpaXML\HandlerCollections;


use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Handler\Handler;
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
        array_walk($handlers, function(AbstractHandler $handler, string $key){
            $this->set($key, $handler);
        });
        reset($handlers);
   }

    public function all(): array
    {
        return $this->handlers;
    }

    public function set(string $key, AbstractHandler $handler): void
    {
        $this->handlers[$key] = $handler;
    }

    public function get(string $key): ?AbstractHandler
    {
        return $this->handlers[$key] ?? null;
    }

    public function remove(string $key): void
    {
        // TODO: Implement remove() method.
    }

    public function equals(HandlersCollectionInterface $handlers): bool
    {
        foreach($this->all() as $key => $handler){
            if(!$handlers->get($key)){
                return false;
            }
        }

        return $this->count() === $handlers->count();
    }

    public function count()
    {
        return count($this->handlers);
    }
}