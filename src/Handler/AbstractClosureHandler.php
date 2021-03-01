<?php


namespace AndyDorff\SherpaXML\Handler;


abstract class AbstractClosureHandler
{
    private $handle;

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
        return $this->asClosure()->call($this, ...func_get_args());
    }
}