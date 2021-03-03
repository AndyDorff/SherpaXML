<?php


namespace AndyDorff\SherpaXML\Interfaces;


interface HandlerInterface
{
    public function asClosure(): \Closure;
    public function __invoke();
}