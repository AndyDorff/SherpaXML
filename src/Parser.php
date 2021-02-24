<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Misc\ParseResult;
use ReflectionFunction;
use XMLReader;

final class Parser
{
    private SherpaXML $xml;
    private XMLReader $xmlReader;

    public function __construct(SherpaXML $xml)
    {
        $this->xml = $xml;
        $this->xmlReader = $xml->xmlReader();
    }

    public function parse(): ParseResult
    {
        $result = new ParseResult();
        foreach($this->moveToNextElement() as $nodeType){
            if($nodeType === XMLReader::ELEMENT){
                $this->doParse($result);
            }
        }

        return $result;
    }

    private function moveToNextElement(): ?\Generator
    {
        do {
            $nodeType = $this->xmlReader->nodeType;
            $isElement = ($nodeType === XMLReader::ELEMENT || $nodeType === XMLReader::END_ELEMENT);
            if ($isElement) {
                yield $nodeType;
            }
        } while ($this->xmlReader->read());

        return null;
    }

    private function doParse(ParseResult $result): void
    {
        $result->totalCount++;
        if($handler = $this->xml->getHandler($this->xmlReader->name)){
            $params = $this->resolveHandleParams($handler->asClosure());
            $handler->__invoke(...$params);
            $result->parseCount++;
        }
    }

    private function resolveHandleParams(\Closure $handle)
    {
        $result = [];
        $handle = new ReflectionFunction($handle);
        foreach($handle->getParameters() as $key => $parameter){
            $name = $parameter->getType()->getName();
            switch($name){
                case \SimpleXMLElement::class:
                    $result[$key] = new \SimpleXMLElement($this->xmlReader->readOuterXml());
                    break;
                case SherpaXML::class:
                    $result[$key] = new SherpaXML($this->xmlReader);
                    break;
                default:
                    $result[$key] = null;
            }
        }

        return $result;
    }

}
