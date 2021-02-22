<?php


namespace AndyDorff\SherpaXML\Handler;


abstract class AbstractClosureHandler extends AbstractHandler
{
    private \Closure $closure;

    public function __construct(\Closure $closure)
    {
        parent::__construct();
        $this->closure = $closure;
    }

    public function handle()
    {
        return $this->closure->call($this);
    }
}