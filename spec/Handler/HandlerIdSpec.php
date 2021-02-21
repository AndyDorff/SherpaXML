<?php

namespace spec\AndyDorff\SherpaXML\Handler;

use AndyDorff\SherpaXML\Handler\HandlerId;
use PhpSpec\ObjectBehavior;

/**
 * Class HandlerIdSpec
 * @package spec\AndyDorff\SherpaXML\Handler
 * @mixin HandlerId
 */
class HandlerIdSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HandlerId::class);
    }

    function it_should_be_Stringify()
    {
        $this->__toString()->shouldBeString();
    }

    function it_should_be_constructed_by_string()
    {
        $handlerId = 'XmlTag';

        $this->beConstructedThrough('fromString', [$handlerId]);
        $this->__toString()->shouldBe($handlerId);
    }
}
