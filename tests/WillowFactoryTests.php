<?php

namespace Tests;

use Tests\Factories\BasicApiFactory;

use function PHPUnit\Framework\assertEquals;

class WillowFactoryTests extends TestCase
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
}
