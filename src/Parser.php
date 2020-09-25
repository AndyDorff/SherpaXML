<?php

namespace AndyDorff\SherpaXML;

final class Parser
{
    private $xml;

    public function __construct(string $xml)
    {
        $this->setXML($xml);
    }

    private function setXML(string $xml)
    {
        $this->xml = new \XMLReader();
        $this->xml->XML($xml);
        $this->xml->read();
        $this->xml->setParserProperty(\XMLReader::VALIDATE, true);
        if(!$this->xml->isValid()){
            throw new \Exception('Given "xml" is not a valid XML');
        }
    }
}
