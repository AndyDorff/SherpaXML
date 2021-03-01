<?php


namespace AndyDorff\SherpaXML\Interpreters;


use AndyDorff\SherpaXML\SherpaXML;

/**
 * Class AbstractInterpreter
 * @package AndyDorff\SherpaXML\Interpreters
 * @method bool isReady(...$params)
 */
abstract class AbstractInterpreter
{
    abstract public function className(): string;
    abstract public function interpret(SherpaXML $xml);
}