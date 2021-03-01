<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Interpreters\AbstractInterpreter;
use AndyDorff\SherpaXML\Misc\ParseResult;
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
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parser::class);
    }

    function it_should_return_all_registered_interpreters()
    {
        $this->interpreters()->shouldHaveCount(1);
    }

    function it_should_register_interpreter_for_handle_parameter(AbstractInterpreter $interpreter)
    {
        $className = 'SomeClassName';
        $interpreter->className()->willReturn($className);

        $interpreters = $this->interpreters()->getWrappedObject();
        $this->registerInterpreter($interpreter);

        $this->interpreters()->shouldBe(array_merge($interpreters, [$className => $interpreter]));
    }

    function it_should_register_multiple_interpreters(AbstractInterpreter $interpreter1, AbstractInterpreter $interpreter2)
    {
        $classNames = ['SomeClassName1', 'SomeClassName2'];
        $interpreter1->className()->willReturn($classNames[0]);
        $interpreter2->className()->willReturn($classNames[1]);

        $interpreters = $this->interpreters()->getWrappedObject();
        $this->registerMultipleInterpreters([$interpreter1, $interpreter2]);

        $this->interpreters()->shouldBe(array_merge(
            $interpreters,
            [$classNames[0] => $interpreter1, $classNames[1] => $interpreter2]
        ));
    }

    function it_should_interpret_handle_params_during_parse(\SplHeap $heap)
    {
        $this->xml->on('letter', function (SherpaXML $sherpaXML, SimpleXMLElement $xml) use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $this->parse($this->xml);

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

        $this->parse($this->xml);

        $heap->insert(true)->shouldHaveBeenCalled();
    }

    function it_should_parse_given_xml()
    {
        $this->xml->on('letter', function(SherpaXML $xml, SimpleXMLElement $xmlElement, ParseResult $result){
            $result->payload['root_name'] = $xmlElement->getName();

            $xml->on('text', function(SherpaXML $xml){
                $xml->on('component', function(SherpaXML $xml, ParseResult $result){
                    $result->payload['text_component'][] = $xml->getCurrentElementInfo();
                });
            });
        });

        $result = $this->parse($this->xml);

        $result->parseCount->shouldBe(4);
        $result->totalCount->shouldBe(15);
        $result->payload->shouldBe([
            'root_name' => 'letter',
            'text_component' => [
                ['name' => 'component', 'attributes' => ['translate' => 'yes']],
                ['name' => 'component', 'attributes' => ['translate' => 'no']]
            ]
        ]);
    }

    function it_should_break_parse_process()
    {
        $this->xml->on('letter', function(SherpaXML $xml, SimpleXMLElement $xmlElement, Parser $parser){
            $parser->parseResult()->payload['root_name'] = $xmlElement->getName();
            $parser->break();

            $xml->on('text', function(SherpaXML $xml){
                $xml->on('component', function(SherpaXML $xml, ParseResult $result){
                    $result->payload['text_component'][] = $xml->getCurrentElementInfo();
                });
            });
        });

        $result = $this->parse($this->xml);

        $result->parseCount->shouldBe(1);
        $result->totalCount->shouldBe(1);
        $result->payload->shouldBe([
            'root_name' => 'letter'
        ]);
    }

    function it_should_use_already_ready_handler_params()
    {
        $interpreter = new class extends AbstractInterpreter{

            public function className(): string
            {
                return \stdClass::class;
            }

            public function interpret(SherpaXML $xml)
            {
                $dto = (object)['title' => null, 'salutation' => null, 'component' => null];
                $xml->on('title', fn(SherpaXML $xml) => $dto->title = $xml->xmlReader()->readInnerXml());
                $xml->on('salutation', fn(SherpaXML $xml) => $dto->salutation = $xml->xmlReader()->readInnerXml());
                $xml->on('component', fn(SherpaXML $xml) => $dto->component = $xml->xmlReader()->readInnerXml());

                return $dto;
            }

            public function isReady(\stdClass $dto): bool
            {
                return isset($dto->title, $dto->salutation, $dto->component);
            }
        };

        $this->beConstructedWith([$interpreter]);

        $this->xml->on('letter', function(\stdClass $letter, Parser $parser){
            $parser->parseResult()->payload['letter'] = $letter;
            $parser->break();
        });

        $result = $this->parse($this->xml);

        $result->payload['letter']->title->shouldBe(' Quote Letter ');
        $result->payload['letter']->salutation->shouldBe('Dear Daniel,');
        $result->payload['letter']->component->shouldBe('Separator');
    }
}