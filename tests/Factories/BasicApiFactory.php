<?php

namespace Tests\Factories;

use Willow\WillowFactory;

class BasicApiFactory extends WillowFactory
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
