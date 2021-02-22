<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\SimpleXMLHandler;
use AndyDorff\SherpaXML\Misc\ParseResult;
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
            if($handler instanceof SimpleXMLHandler){
                $simpleXml = new \SimpleXMLElement($this->xmlReader->readOuterXml());
                $handler->__invoke($simpleXml);
            } else {
                $handler->__invoke();
            }
            $result->parseCount++;
        }
    }

}
