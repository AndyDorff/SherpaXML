<?php


namespace AndyDorff\SherpaXML\Misc;


use AndyDorff\SherpaXML\Handler\AbstractClosureHandler;
use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Interpreters\AbstractInterpreter;

class DeferredHandler
{
    /**
     * @var AbstractInterpreter
     */
    private AbstractInterpreter $interpreter;
    /**
     * @var mixed
     */
    private $result;
    /**
     * @var AbstractHandler
     */
    private AbstractClosureHandler $handler;
    private array $handlerParams = [];

    public function __construct(AbstractInterpreter $interpreter, $result)
    {
        $this->interpreter = $interpreter;
        $this->result = $result;
    }

    public function setHandler(AbstractClosureHandler $handler, array $params): void
    {
        $this->handler = $handler;
        $this->handlerParams = $params;
    }

    public function invoke(): void
    {
        $this->handler->__invoke(...$this->handlerParams);
    }

    public function isReady(): bool
    {
        return $this->interpreter->isReady($this->result);
    }
}