<?php

namespace Tests\Fields;

use Tests\TestCase;
use Willow\Fields\FieldHelper;
use Willow\Fields\Resolver;

class ResolverTest extends TestCase
{
    /** @test */
    public function it_updates_fields_that_implement_FieldHelper()
    {
        $nonResolver = new DontResolve;
        $input = [
            'one' => 1,
            'four' => new AlwaysFour,
            'not_four' => $nonResolver,
            'two' => 2,
            'other_four' => new AlwaysFour,
        ];

        $output = [
            'one' => 1,
            'four' => 4,
            'not_four' => $nonResolver,
            'two' => 2,
            'other_four' => 4,
        ];

        $this->assertEquals($output, (new Resolver)($input));
    }

    /** @test */
    public function it_updates_fields_in_nested_arrays()
    {
        $input = [
            'one' => 1,
            'array' => [
                'childArray' => [
                    'four' => new AlwaysFour,
                ],
                'four' => new AlwaysFour,
            ],
            'four' => new AlwaysFour,
        ];

        $output = [
            'one' => 1,
            'array' => [
                'childArray' => [
                    'four' => 4,
                ],
                'four' => 4,
            ],
            'four' => 4,
        ];

        $this->assertEquals($output, (new Resolver)($input));
    }
}

class AlwaysFour implements FieldHelper
{
    public function resolve(array $madeFactory): mixed
    {
        return 4;
    }
}

class DontResolve
{
    public function resolve(array $madeFactory): mixed
    {
        return 4;
    }
}
