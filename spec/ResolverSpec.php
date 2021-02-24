<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Resolver;
use AndyDorff\SherpaXML\SherpaXML;
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

    function it_should_resolve_any_AbstractHandler_handler()
    {
        $handler = new class extends AbstractHandler{
            public function handle()
            {
                return true;
            }
        };

        $this->resolve($handler)->shouldReturn($handler);
    }
}
