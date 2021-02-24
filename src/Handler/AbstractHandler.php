<?php


namespace AndyDorff\SherpaXML\Handler;


/**
 * Class AbstractHandler
 * @package AndyDorff\SherpaXML\Handler
 * @method handle()
 */
abstract class AbstractHandler extends AbstractClosureHandler
{
    public function __construct()
    {
        parent::__construct(\Closure::fromCallable([$this, 'handle']));
    }
}