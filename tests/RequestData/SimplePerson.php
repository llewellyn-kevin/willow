<?php

namespace Tests\RequestData;

use Willow\RequestData;

/**
 * @property string $name
 * @property int $age
 */
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
