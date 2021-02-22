<?php


namespace AndyDorff\SherpaXML\Handler;


class SimpleXMLHandler extends AbstractHandler
{
    private ?\SimpleXMLElement $xml;

    public function __construct(\SimpleXMLElement $xml = null)
    {
        $this->xml = $xml;// ?? new \SimpleXMLElement('');
    }

    protected function handle()
    {
        return $this->xml;
    }
}