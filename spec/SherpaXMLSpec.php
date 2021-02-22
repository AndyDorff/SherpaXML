<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Exceptions\FileNotFoundException;
use AndyDorff\SherpaXML\Exceptions\InvalidXMLFileException;
use AndyDorff\SherpaXML\Handler\Handler;
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
        $handler = new Handler();
        $this->on('letter', $handler);
        $this->getHandler('letter')->shouldReturn($handler);
    }

}
