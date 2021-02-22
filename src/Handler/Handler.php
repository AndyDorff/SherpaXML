<?php


namespace AndyDorff\SherpaXML\Handler;


final class Handler extends AbstractClosureHandler
{
    public function __construct(\Closure $handle = null)
    {
        parent::__construct($handle ?? function(){});
    }
}