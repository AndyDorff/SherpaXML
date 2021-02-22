<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;
use AndyDorff\SherpaXML\Handler\SimpleXMLHandler;
use AndyDorff\SherpaXML\Resolver;
use PhpSpec\ObjectBehavior;
use SimpleXMLElement;

/**
 * Class ResolverSpec
 * @package spec\AndyDorff\SherpaXML
 * @mixin Resolver
 */
class ResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Resolver::class);
    }

    function it_should_resolve_callback_handler()
    {
        $handler = $this->resolve(function(){
            return true;
        });

        $handler->shouldBeAnInstanceOf(Handler::class);
    }

    function it_should_resolve_Handler_handler()
    {
        $handler = new Handler();

        $this->resolve($handler)->shouldReturn($handler);
    }

    function it_should_resolve_SimpleXML_handler(\SplHeap $heap)
    {
        $handler = $this->resolve(function (SimpleXMLElement $xml) {
            return true;
        });

        $handler->shouldBeAnInstanceOf(SimpleXMLHandler::class);
    }

}
