<?php


namespace AndyDorff\SherpaXML\Interpreters;


use AndyDorff\SherpaXML\SherpaXML;

abstract class AbstractInterpreter
{
    abstract public function className(): string;
    abstract public function interpret(SherpaXML $xml);
}