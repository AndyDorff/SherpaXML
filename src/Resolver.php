<?php


namespace AndyDorff\SherpaXML;


use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\SimpleXMLHandler;
use ReflectionFunction;

final class Resolver
{
    public function resolve($handler): AbstractHandler
    {
        switch(true){
            case ($handler instanceof AbstractHandler):
                break;
            case (is_callable($handler)):
                $handler = $this->resolveByCallable($handler);
                break;
            default:
                $handler = new Handler();
        }

        return $handler;
    }

    private function resolveByCallable(callable $handler): AbstractHandler
    {
        $closure = \Closure::fromCallable($handler);
        $reflection = new ReflectionFunction(\Closure::fromCallable($handler));
        switch($this->getClosureType($reflection)){
            case 'simple_xml':
                $handler = new SimpleXMLHandler($closure);
                break;
            default:
                $handler = new Handler($closure);
        }

        return $handler;
    }

    private function getClosureType(ReflectionFunction $closure): string
    {
        $type = '';
        foreach($closure->getParameters() as $parameter){
            if(
                $parameter->getType() instanceof \ReflectionNamedType
                && $parameter->getType()->getName() === 'SimpleXMLElement'
            ){
                $type = 'simple_xml';
            }
        }

        return $type;
    }
}