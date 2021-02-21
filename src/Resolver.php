<?php


namespace AndyDorff\SherpaXML;


use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;

final class Resolver
{
    public function resolve($handler, HandlerId $handlerId = null): Handler
    {
        $handlerId = $handlerId ?? new HandlerId();
        switch(true){
            case (is_callable($handler)):
                $handler = $this->resolveByCallable($handler, $handlerId);
                break;
            default:
                $handler = new Handler($handlerId);
        }

        return $handler;
    }

    private function resolveByCallable(callable $handler, HandlerId $handlerId): Handler
    {
        return new Handler($handlerId, $handler);
    }
}