<?php

namespace Tests\RequestData;

use Willow\RequestData;

/**
 * @property string $name
 * @property int $age
 */
class FakePerson extends RequestData
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'age' => 42,
        ];
    }
}
