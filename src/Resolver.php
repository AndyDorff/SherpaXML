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
        $this->resolveHandlerParams($handler);

        return $handler;
    }

    private function resolveHandlerParams(AbstractClosureHandler $handler)
    {
        $reflection = new ReflectionFunction($handler->asClosure());
        foreach ($reflection->getParameters() as $parameter){

        }
    }

    private function resolveByCallable(callable $handler): AbstractClosureHandler
    {
        $closure = \Closure::fromCallable($handler);
        $reflection = new ReflectionFunction(\Closure::fromCallable($handler));
        switch($this->getClosureType($reflection)){
            case 'simple_xml':
                $handler = new SimpleXMLHandler($closure);
                break;
            case 'sherpa_xml':
                $handler = new SherpaXMLHandler($closure);
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
            if($parameter->getType() instanceof \ReflectionNamedType){
                switch($parameter->getType()->getName()){
                    case \SimpleXMLElement::class:
                        $type = 'simple_xml';
                        break;
                    case SherpaXML::class:
                        $type = 'sherpa_xml';
                        break;
                }
            }
        }

        return $type;
    }
}