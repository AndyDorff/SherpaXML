<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Parser;
use AndyDorff\SherpaXML\SherpaXML;
use PhpSpec\ObjectBehavior;

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
}
