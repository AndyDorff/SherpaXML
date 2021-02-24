<?php


namespace AndyDorff\SherpaXML\Handler;


abstract class AbstractHandler
{
    public function __construct()
    {
    }

    abstract protected function handle();

    /**
     * @return mixed
     */
    final public function __invoke()
    {
        return call_user_func_array([$this, 'handle'], func_get_args());
    }
}