<?php

namespace Tests;

use Tests\Factories\BasicApiFactory;
use Tests\Factories\ComposedApiFactory;

class FactoryTests extends TestCase
{
    /** @test */
    public function it_creates_a_fake_api_response()
    {
        $api = new BasicApiFactory();
        self::assertEquals(
            ['quote' => 'Hello there'],
            $api->make(),
        );
    }

    /** @test */
    public function it_wraps_response_data()
    {
        $api = new ComposedApiFactory();
        self::assertEquals(
            [
                'status' => 200,
                'response' => ['quote' => 'Hello there'],
            ],
            $api->make(),
        );
    }
}
