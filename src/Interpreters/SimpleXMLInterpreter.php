<?php


namespace AndyDorff\SherpaXML\Interpreters;


use AndyDorff\SherpaXML\SherpaXML;

class SimpleXMLInterpreter extends AbstractInterpreter
{
    public function className(): string
    {
        return \SimpleXMLElement::class;
    }

    public function interpret(SherpaXML $xml)
    {
        return new \SimpleXMLElement($xml->xmlReader()->readOuterXml());
    }
}