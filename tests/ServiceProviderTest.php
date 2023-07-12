<?php

namespace Tests;

use Willow\Dispatcher;
use Willow\Willow;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_the_willow_facade()
    {
        $this->assertEquals('pong', Willow::ping());
    }
}
