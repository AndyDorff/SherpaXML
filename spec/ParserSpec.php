<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Interpreters\AbstractInterpreter;
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

    function it_should_return_all_registered_interpreters()
    {
        $this->interpreters()->shouldBe([]);
    }

    function it_should_register_interpreter_for_handle_parameter(AbstractInterpreter $interpreter)
    {
        $className = 'SomeClassName';
        $interpreter->className()->willReturn($className);

        $this->registerInterpreter($interpreter);

        $this->interpreters()->shouldBe([$className => $interpreter]);
    }

    function it_should_register_multiple_interpreters(AbstractInterpreter $interpreter1, AbstractInterpreter $interpreter2)
    {
        $classNames = ['SomeClassName1', 'SomeClassName2'];
        $interpreter1->className()->willReturn($classNames[0]);
        $interpreter2->className()->willReturn($classNames[1]);

        $this->registerMultipleInterpreters([$interpreter1, $interpreter2]);

        $this->interpreters()->shouldBe([$classNames[0] => $interpreter1, $classNames[1] => $interpreter2]);
    }

    function it_should_interpret_handle_params_during_parse(\SplHeap $heap)
    {
        $this->xml->on('letter', function (SherpaXML $sherpaXML, SimpleXMLElement $xml) use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $this->parse();

        $heap->insert(true)->shouldHaveBeenCalled();
    }

    function it_should_interpret_Handler_params_during_parse(\SplHeap $heap)
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
