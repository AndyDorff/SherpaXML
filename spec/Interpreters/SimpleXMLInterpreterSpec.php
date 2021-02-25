<?php

namespace spec\AndyDorff\SherpaXML\Interpreters;

use AndyDorff\SherpaXML\Interpreters\SimpleXMLInterpreter;
use AndyDorff\SherpaXML\SherpaXML;
use PhpSpec\ObjectBehavior;

class SimpleXMLInterpreterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SimpleXMLInterpreter::class);
    }

    function it_should_return_class_name_to_interpret()
    {
        $this->className()->shouldReturn(\SimpleXMLElement::class);
    }

    function it_should_interpret_SimpleXmlElement()
    {
        $xml = SherpaXML::open(__DIR__.'/../resources/sample.xml');
        $result = $this->interpret($xml);
        $result->shouldBeAnInstanceOf(\SimpleXMLElement::class);
    }
}
