<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\HandlerId;
use AndyDorff\SherpaXML\Interfaces\HandlersCollectionInterface;
use AndyDorff\SherpaXML\Misc\ParseResult;
use XMLReader;

final class Parser
{
    private XMLReader $xmlReader;
    /**
     * @var HandlersCollectionInterface
     */
    private HandlersCollectionInterface $handlers;

    public function __construct(SherpaXML $xml)
    {
        $this->xmlReader = $xml->xmlReader();
        $this->handlers = $xml->handlers();
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
        if($handler = $this->handlers->get(HandlerId::fromString($this->xmlReader->name))){
            $handler->__invoke();
            $result->parseCount++;
        }
    }

}
