<?php

namespace Tests\Fields;

use Tests\TestCase;
use Willow\Fields\Duplicate;
use Willow\Fields\Resolver;

class DuplicateTest extends TestCase
{
    /** @test */
    public function it_duplicates_an_existing_field()
    {
        $this->assertEquals(
            ['one' => 1, 'other_one' => 1],
            (new Resolver)(['one' => 1, 'other_one' => new Duplicate('one')]),
        );
    }

    /** @test */
    public function it_defaults_to_null()
    {
        $this->assertEquals(
            ['one' => 1, 'null' => null],
            (new Resolver)(['one' => 1, 'null' => new Duplicate('missing_field')]),
        );
    }

    /** @test */
    public function it_works_on_nested_arrays_using_dot_notation()
    {
        $this->assertEquals(
            ['first' => ['one' => 1], 'second' => ['other_one' => 1]],
            (new Resolver)(['first' => ['one' => 1], 'second' => ['other_one' => new Duplicate('first.one')]]),
        );
    }
}
