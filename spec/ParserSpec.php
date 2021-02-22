<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Parser;
use AndyDorff\SherpaXML\SherpaXML;
use PhpSpec\ObjectBehavior;
use Prophecy\Prophet;

/**
 * Class ParserSpec
 * @package spec\AndyDorff\SherpaXML
 * @mixin Parser
 */
class ParserSpec extends ObjectBehavior
{
    /**
     * @var SherpaXML
     */
    private $xml;

    function let()
    {
        $this->xml = SherpaXML::open(__DIR__.'/resources/sample.xml');
        $this->beConstructedWith($this->xml);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parser::class);
    }

    function it_should_parse_given_xml(\SplHeap $heap)
    {
        $this->xml->on('letter', function() use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $result = $this->parse();

        $heap->insert(true)->shouldHaveBeenCalled();
        $result->parseCount->shouldBe(1);
        $result->totalCount->shouldBe(15);
    }

    /**
     * @param \SplHeap $heap
     */
    function it_should_parse_xml_on_SimpleXml_demand(\SplHeap $heap)
    {
        $simpleXml = null;
        $this->xml->on('letter', function(\SimpleXMLElement $xml) use ($heap, &$simpleXml){
            $heap->getWrappedObject()->insert($xml);
            $simpleXml = $xml;
        });

        $result = $this->parse();

        $heap->insert($simpleXml)->shouldHaveBeenCalled();
        $result->parseCount->shouldBe(1);
    }
}
