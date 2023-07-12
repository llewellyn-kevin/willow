<?php

namespace Tests;

use Tests\RequestData\SimplePerson;
use Willow\RequestData;

class RequestDataTest extends TestCase
{
    /** @test */
    public function it_accesses_data_from_array_as_properties()
    {
        $person = (new SimplePerson)->make();
        $this->assertEquals('John Wick', $person->name);
    }

    /** @test */
    public function missing_properties_default_to_null()
    {
        $person = (new SimplePerson)->make();
        $this->assertNull($person->fake);
    }
}
