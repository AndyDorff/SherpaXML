<?php

namespace spec\AndyDorff\SherpaXML\Handler;

use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;
use PhpSpec\ObjectBehavior;

/**
 * Class HandlerSpec
 * @package spec\AndyDorff\SherpaXML\Handler
 * @mixin Handler
 */
class HandlerSpec extends ObjectBehavior
{
    private $handlerId;

    function let()
    {
        $this->handlerId = new HandlerId();
        $this->beConstructedWith($this->handlerId);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Handler::class);
    }

    function it_should_be_constructed_with_id()
    {
        $this->beConstructedWith($this->handlerId);
        $this->shouldNotThrow(\Throwable::class)->duringInstantiation();
    }

//    function it_can_has_it_own_inner_handle()
//    {
//
//    }

    function it_can_be_constructed_with_outer_handle(\SplHeap $heap)
    {
        $this->beConstructedWith($this->handlerId, function() use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $this->__invoke();

        $heap->insert(true)->shouldHaveBeenCalled();
    }

    function it_can_delegate_handle_to_outer_method(\SplHeap $heap)
    {
        $this->delegate(function() use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $this->__invoke();

        $heap->insert(true)->shouldHaveBeenCalled();
    }

    function it_should_return_id()
    {
        $this->id()->shouldBe($this->handlerId);
    }
}
