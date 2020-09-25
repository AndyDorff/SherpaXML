<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Parser;
use PhpSpec\Exception\Exception;
use PhpSpec\ObjectBehavior;

class ParserSpec extends ObjectBehavior
{
    private $xml;

    function let()
    {
        $this->xml  = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<XML>
    <Groups>
        <Group1>Value1</Group1>
        <Group2>
            Value2
            <Group1>Value2.1</Group1>
        </Group2>
        <Group3>Value3</Group3>
    </Groups>
</XML>
XML;
        $this->beConstructedWith($this->xml);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parser::class);
    }

    function it_should_be_constructed_with_xml_string()
    {
        $this->shouldNotThrow(\Exception::class)->duringInstantiation();
    }

    function it_should_be_constructed_with_valid_xml_string()
    {
        $this->beConstructedWith('<a>NO XML STRING</b>');
        $this->shouldThrow(\Exception::class)->duringInstantiation();
    }
}
