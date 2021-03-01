<?php

namespace spec\AndyDorff\SherpaXML\HandlerCollections;

use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;
use AndyDorff\SherpaXML\HandlerCollections\InMemoryHandlersCollection;
use PhpSpec\ObjectBehavior;

class InMemoryHandlersCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InMemoryHandlersCollection::class);
    }

    function it_can_set_some_handlers_while_constructed()
    {
        $this->shouldNotThrow(\Throwable::class)->duringInstantiation();
        $this->beConstructedWith($handlers = [
            new Handler()
        ]);
        $this->all()->shouldHaveCount(count($handlers));
    }

    function it_should_check_equality_with_another_handlers_collection()
    {
        $handlers = [new Handler()];
        $this->beConstructedWith($handlers);

        $sameHandlers = new InMemoryHandlersCollection($handlers);
        $anotherHandlers = new InMemoryHandlersCollection([
            new Handler(),
            new Handler()
        ]);

        $this->equals($sameHandlers)->shouldReturn(true);
        $this->equals($anotherHandlers)->shouldReturn(false);
    }

    function it_should_remove_handler_by_key()
    {
        $handlers = ['handler' => new Handler()];
        $this->beConstructedWith($handlers);

        $this->remove('handler');

        $this->all()->shouldReturn([]);
    }
}
