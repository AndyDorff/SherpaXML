<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Exceptions\FileNotFoundException;
use AndyDorff\SherpaXML\Exceptions\InvalidXMLFileException;
use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Parser;
use AndyDorff\SherpaXML\SherpaXML;
use PhpSpec\ObjectBehavior;

/**
 * Class SherpaXMLSpec
 * @package spec\AndyDorff\SherpaXML
 * @mixin SherpaXML
 */
class SherpaXMLSpec extends ObjectBehavior
{
    private string $xmlPath;
    private \XMLReader $xmlReader;

    function let()
    {
        $this->xmlPath = __DIR__.'/resources/sample.xml';
        $this->xmlReader = (new \XMLReader());
        $this->xmlReader->open($this->xmlPath);

        $this->beConstructedWith($this->xmlReader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SherpaXML::class);
    }

    function it_should_be_constructed_by_xml_file()
    {
        $this->beConstructedThrough('open', [$this->xmlPath]);
        $this->xmlReader()->shouldBeAnInstanceOf(\XMLReader::class);
    }

    function it_should_throw_exception_if_xml_file_not_found_during_open()
    {
        $notExistsXmlPath = __DIR__.'/resources/not_exists.xml';

        $this->shouldThrow(FileNotFoundException::class)->during('open', [$notExistsXmlPath]);
    }

    function it_should_throw_exception_if_xml_file_is_invalid_during_open()
    {
        $invalidXmlPath = __DIR__.'/resources/invalid.xml';

        $this->shouldThrow(InvalidXMLFileException::class)->during('open', [$invalidXmlPath]);
    }

    function it_should_return_all_registered_handlers()
    {
        $this->handlers()->shouldBeArray();
    }

    function it_should_register_handler_for_xml_tag()
    {
        $handler1 = new Handler();
        $handler2 = new Handler(function(SherpaXML $xml) use ($handler1){
            $xml->on('title', $handler1);
        });
        $this->on('letter', $handler2);

        (new Parser())->parse($this->getWrappedObject());

        $this->getHandler('/letter')->shouldReturn($handler2);
        $this->getHandler('/letter/title')->shouldReturn($handler1);
    }

    function it_should_return_current_node()
    {
        $this->current();

        $this->getCurrentNodeType()->shouldBe(\XMLReader::NONE);
    }

    function it_should_move_to_next_node()
    {
        $this->next();

        $this->getCurrentNodeType()->shouldBe(\XMLReader::COMMENT);
    }

    function it_should_be_an_Iterator()
    {
        $this->shouldBeAnInstanceOf(\Iterator::class);
    }

    function it_should_move_to_next_xml_node()
    {
        $this->moveToNextNodeByType(\XMLReader::COMMENT);

        $this->getCurrentElementInfo()['name']->shouldBe('#comment');
    }

    function it_should_move_to_next_xml_element()
    {
        $this->moveToNextElement();

        $this->getCurrentElementInfo()['name']->shouldBe('letter');
    }

    function it_should_move_to_end()
    {
        $this->moveToEnd();

        $this->getCurrentNodeType()->shouldBe(\XMLReader::NONE);
        $this->key()->shouldBe(59);

        $this->moveToEnd();

        $this->getCurrentNodeType()->shouldBe(\XMLReader::NONE);
        $this->key()->shouldBe(59);
    }

    function it_should_extract_handler_by_tag()
    {
        $handler = function () { return true;};
        $this->on('letter', $handler);

        $extractedHandler = $this->extractHandler('letter');

        $extractedHandler->__invoke()->shouldReturn(true);
        $this->handlers()->shouldBe([]);
    }

    function it_should_register_handle_for_same_node_in_the_node_handler(\SplHeap $heap)
    {
        $this->on('letter', function(SherpaXML $xml) use ($heap){
            $xml->on('/', function() use ($heap){
                $heap->getWrappedObject()->insert(true);
            });
        });

        (new Parser())->parse($this->getWrappedObject());

        $heap->insert(true)->shouldHaveBeenCalled();
    }
}
