<?php

namespace Tests\Factories;

use Willow\Factory;

class BasicApiFactory extends Factory
{
    /**
     * Mocks a simple API response that returns a quote.
     * @return array
     */
    public function definition(): array
    {
        return [
            'quote' => 'Hello there',
        ];
    }
}
