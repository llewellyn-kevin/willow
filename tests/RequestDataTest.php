<?php

namespace Tests;

use Tests\RequestData\FakePerson;
use Tests\RequestData\SimplePerson;

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

    /** @test */
    public function it_returns_data_as_array()
    {
        $actual = (new SimplePerson)->make()->toArray();
        $this->assertEquals(
            ['name' => 'John Wick', 'age' => 42],
            $actual,
        );
    }

    /** @test */
    public function it_has_seedable_faker()
    {
        $actual = (new FakePerson)->seedFaker(11201)->make();

        $this->assertEquals('Melody Schaden', $actual->name);
        $this->assertEquals(42, $actual->age);
    }
}
