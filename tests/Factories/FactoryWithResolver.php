<?php

namespace Tests\Factories;

use Willow\Factory;
use Willow\Fields\Duplicate;

class FactoryWithResolver extends Factory
{
    public function definition(): array
    {
        return [
            'first' => 'anna',
            'duplicate' => new Duplicate('first'),
        ];
    }
}
