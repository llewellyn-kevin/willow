<?php

namespace Tests\RequestData;

use Willow\RequestData;

class SimplePerson extends RequestData
{
    public function definition(): array
    {
        return [
            'name' => 'John Wick',
            'age' => 42,
        ];
    }
}
