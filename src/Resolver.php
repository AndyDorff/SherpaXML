<?php


namespace AndyDorff\SherpaXML;


use AndyDorff\SherpaXML\Handler\AbstractClosureHandler;
use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\SherpaXMLHandler;
use AndyDorff\SherpaXML\Handler\SimpleXMLHandler;
use ReflectionFunction;

final class Resolver
{
    public function resolve($handler): AbstractClosureHandler
    {
        if(!($handler instanceof AbstractClosureHandler)){
            $closure = is_callable($handler) ? \Closure::fromCallable($handler) : null;
            $handler = new Handler($closure);
        }

        return $handler;
    }

    public function resolveValue(&$value): AbstractClosureHandler
    {
        return new Handler((function (SherpaXML $xml) use (&$value){
            $value = $xml->xmlReader()->readInnerXml();
        }));
    }
}