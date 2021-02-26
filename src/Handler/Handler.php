<?php


namespace AndyDorff\SherpaXML\Handler;


final class Handler extends AbstractClosureHandler
{
    public function __construct(\Closure $handle = null)
    {
        $handle = $handle ?? \Closure::fromCallable(function(){});

        parent::__construct($handle);
    }
}