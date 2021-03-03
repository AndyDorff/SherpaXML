<?php


namespace AndyDorff\SherpaXML\Misc;


use AndyDorff\SherpaXML\Handler\AbstractClosureHandler;
use AndyDorff\SherpaXML\Interpreters\AbstractInterpreter;

final class HandlerManager
{
    /**
     * @var AbstractClosureHandler
     */
    private AbstractClosureHandler $handler;
    private string $tagPath = '';
    private array $params = [];

    private ?AbstractInterpreter $interpreter = null;
    /**
     * @var mixed
     */
    private $interpreterResult;
    private bool $invoked = false;

    public function __construct(AbstractClosureHandler $handler, string $tagPath)
    {
        $this->handler = $handler;
        $this->tagPath = $tagPath;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function handler(): AbstractClosureHandler
    {
        return $this->handler;
    }

    public function tagPath(): string
    {
        return $this->tagPath;
    }

    public function isNestedFor(HandlerManager $prevHandler): bool
    {
        return ($this->tagPath !== $prevHandler->tagPath && strpos($this->tagPath, $prevHandler->tagPath()) !== false);
    }

    public function waitForInterpreter(AbstractInterpreter $interpreter, $result): void
    {
        $this->interpreter = $interpreter;
        $this->interpreterResult = $result;
    }

    public function isDeferred(): bool
    {
        return isset($this->interpreter);
    }

    public function invoke(bool $force = false): void
    {
        if(!$this->invoked && ($this->isReady() || $force)){
            $this->handler->__invoke(...$this->params);
            $this->invoked = true;
        }
    }

    public function complete()
    {
        $this->handler->complete();
    }

    private function isReady(): bool
    {
        return ($this->interpreter
            ? $this->interpreter->isReady($this->interpreterResult)
            : true
        );
    }

}