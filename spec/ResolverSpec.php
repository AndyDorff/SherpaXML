<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;
use AndyDorff\SherpaXML\Resolver;
use PhpSpec\ObjectBehavior;

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
        foreach([null, HandlerId::fromString('XmlTag')] as $i => $handlerId){
            $handler = $this->resolve(function(){
                return true;
            }, $handlerId);

            $handler->shouldBeAnInstanceOf(Handler::class);
            if($i){
                $handler->id()->shouldBe($handlerId);
            } else {
                $handler->id()->shouldNotBe(null);
            }
        }
    }
}
