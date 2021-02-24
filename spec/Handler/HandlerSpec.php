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
    function let()
    {
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Handler::class);
    }

    function it_can_be_constructed_with_outer_handle(\SplHeap $heap)
    {
        $this->beConstructedWith(function() use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        $this->__invoke();

        $heap->insert(true)->shouldHaveBeenCalled();
    }

    function it_should_return_handle_as_closure(\SplHeap $heap)
    {
        $this->beConstructedWith(function() use ($heap){
            $heap->getWrappedObject()->insert(true);
        });

        /**
         * @var \Closure $closure
         */
        $closure = $this->asClosure();
        $closure->call($this->getWrappedObject());

        $closure->shouldBeAnInstanceOf(\Closure::class);
        $heap->insert(true)->shouldHaveBeenCalled();
    }
}
