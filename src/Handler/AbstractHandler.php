<?php


namespace AndyDorff\SherpaXML\Handler;


abstract class AbstractHandler
{
    private \Closure $handle;

    public function __construct()
    {
    }

    public function delegate(\Closure $handle): void
    {
        $this->handle = $handle;
    }

    abstract protected function handle();

    /**
     * @return mixed
     */
    final public function __invoke()
    {
        if($this->handle){
            return call_user_func_array($this->handle, func_get_args());
        } else {
            return call_user_func_array([$this, 'handle'], func_get_args());
        }
    }
}