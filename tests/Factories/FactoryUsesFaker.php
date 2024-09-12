<?php

namespace Tests\Factories;

use Willow\Factory;

class FactoryUsesFaker extends Factory
{
    public function definition(): array
    {
        $this->faker->seed(11201);

        return [
            'fake' => $this->faker->name(),
        ];
    }
}
