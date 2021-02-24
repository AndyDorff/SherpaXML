<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Handler\SimpleXMLHandler;
use AndyDorff\SherpaXML\Parser;
use AndyDorff\SherpaXML\SherpaXML;
use PhpSpec\ObjectBehavior;
use Prophecy\Prophet;
use SimpleXMLElement;

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

    function it_should_resolve_handle_params_during_parse(\SplHeap $heap)
    {
        $this->xml->on('letter', function (SherpaXML $sherpaXML, SimpleXMLElement $xml) use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $this->parse();

        $heap->insert(true)->shouldHaveBeenCalled();
    }

    function it_should_resolve_Handler_params_during_parse(\SplHeap $heap)
    {
        $this->xml->on('letter', new class($heap) extends AbstractHandler{
            private \SplHeap $heap;
            public function __construct($heap){
                parent::__construct();
                $this->heap = $heap->getWrappedObject();
            }
            public function handle(SherpaXML $sherpaXML, SimpleXMLElement $xml){
                $this->heap->insert(true);
            }
        });

        $this->parse();

        $heap->insert(true)->shouldHaveBeenCalled();
    }

}
