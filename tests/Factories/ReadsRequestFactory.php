<?php

namespace Tests\Factories;

use Willow\Factory;

class ReadsRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->readRequest('name', 'Ben Stiller'),
            'key' => $this->readRequest('no_key', 42),
        ];
    }
}
