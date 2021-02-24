<?php


namespace AndyDorff\SherpaXML\Handler;


abstract class AbstractClosureHandler extends AbstractHandler
{
    private \Closure $handle;

    public function __construct(\Closure $handle)
    {
        parent::__construct();
        $this->handle = $handle;
    }

    public function handle()
    {
        return call_user_func_array($this->handle, func_get_args());
    }
}