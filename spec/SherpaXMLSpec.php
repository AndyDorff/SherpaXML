<?php

namespace spec\AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\SherpaXML;
use PhpSpec\ObjectBehavior;

class SherpaXMLSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SherpaXML::class);
    }
}
