<?php

namespace Tests\Factories;

use Willow\Factory;

class ComposedApiFactory extends Factory
{
    /**
     * Determines how the API response data should be formatted before
     * being returned.
     * @param array $generated
     *
     * @return array
     */
    public function compose(array $generated): array
    {
        return [
            'status' => 200,
            'response' => $generated,
        ];
    }

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
