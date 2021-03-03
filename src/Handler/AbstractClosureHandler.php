<?php


namespace AndyDorff\SherpaXML\Handler;


use AndyDorff\SherpaXML\Misc\ParseResult;
use AndyDorff\SherpaXML\SherpaXML;

abstract class AbstractClosureHandler
{
    private \Closure $handle;
    private ?\Closure $completed = null;

    public function __construct(\Closure $handle)
    {
        $this->handle = $handle;
    }

    final public function asClosure(): \Closure
    {
        return $this->handle;
    }

    final public function __invoke()
    {
        return call_user_func_array($this->asClosure(), func_get_args());
    }

    final public function complete(): void
    {
        if($this->completed){
            call_user_func_array($this->completed, func_get_args());
        }
    }

    final public function onComplete(\Closure $completed): void
    {
        $this->completed = $completed;
    }
}