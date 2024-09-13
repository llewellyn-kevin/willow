<?php

namespace Tests;

use Tests\Factories\BasicApiFactory;
use Tests\Factories\ComposedApiFactory;
use Tests\Factories\FactoryUsesFaker;
use Tests\Factories\FactoryWithResolver;
use Tests\Factories\ReadsRequestFactory;
use Tests\RequestData\SimplePerson;

class FactoryTest extends TestCase
{
    /** @test */
    public function it_creates_a_fake_api_response()
    {
        $api = new BasicApiFactory;
        self::assertEquals(
            ['quote' => 'Hello there'],
            $api->make(),
        );
    }

    /** @test */
    public function it_wraps_response_data()
    {
        $api = new ComposedApiFactory;
        self::assertEquals(
            [
                'status' => 200,
                'response' => [
                    'quote' => 'Hello there',
                    'source' => [
                        'name' => 'Ewan McGregor',
                        'age' => 32,
                    ]
                ],
            ],
            $api->make(),
        );
    }

    /** @test */
    public function it_overrides_defaults_with_attribute_array()
    {
        $response = (new ComposedApiFactory)->make([
            'source' => [
                'age' => 23
            ],
            'movie' => [
                'name' => 'Star Wars'
            ],
        ]);

        self::assertEquals(
            [
                'status' => 200,
                'response' => [
                    'quote' => 'Hello there',
                    'source' => [
                        'name' => 'Ewan McGregor',
                        'age' => 23,
                    ],
                    'movie' => [
                        'name' => 'Star Wars'
                    ]
                ],
            ],
            $response,
        );
    }

    /** @test */
    public function it_generates_more_than_one_response()
    {
        self::assertEquals(
            [
                ['quote' => 'Hello there'],
                ['quote' => 'Hello there'],
                ['quote' => 'Hello there'],
            ],
            (new BasicApiFactory)->count(3)->make(),
        );

        $composedResponseData = [
            'quote' => 'Hello there',
            'source' => [
                'name' => 'Ewan McGregor',
                'age' => 32,
            ]
        ];

        self::assertEquals(
            [
                'status' => 200,
                'response' => [
                    $composedResponseData,
                    $composedResponseData,
                    $composedResponseData,
                ],
            ],
            (new ComposedApiFactory)->count(3)->make(),
        );
    }

    /** @test */
    public function it_generates_an_empty_reponse()
    {
        self::assertEquals([], (new BasicApiFactory)->count(0)->make());
        self::assertEquals(
            [
                'status' => 200,
                'response' => [],
            ],
            (new ComposedApiFactory)->count(0)->make(),
        );
    }

    /** @test */
    public function it_calls_lifecycle_hooks_after_making_a_response()
    {
        $counter = 20;
        $closureOne = function (array $data) use (&$counter) {
            $counter++;
            $data['source']['age'] = $counter;
            return $data;
        };
        $responseOne = (new ComposedApiFactory)->count(3)->afterMaking($closureOne)->make();

        self::assertEquals(
            [
                'status' => 200,
                'response' => [
                    [
                        'quote' => 'Hello there',
                        'source' => [
                            'name' => 'Ewan McGregor',
                            'age' => 21,
                        ]
                    ],
                    [
                        'quote' => 'Hello there',
                        'source' => [
                            'name' => 'Ewan McGregor',
                            'age' => 22,
                        ]
                    ],
                    [
                        'quote' => 'Hello there',
                        'source' => [
                            'name' => 'Ewan McGregor',
                            'age' => 23,
                        ]
                    ]
                ]
            ],
            $responseOne,
        );

        $closureTwo = function (array $data) use (&$counter) {
            $data['source']['age'] = $counter * 2;
            return $data;
        };
        $responseTwo = (new ComposedApiFactory)
            ->count(3)
            ->afterMaking($closureOne)
            ->afterMaking($closureTwo)
            ->make();

        self::assertEquals(
            [
                'status' => 200,
                'response' => [
                    [
                        'quote' => 'Hello there',
                        'source' => [
                            'name' => 'Ewan McGregor',
                            'age' => 48,
                        ]
                    ],
                    [
                        'quote' => 'Hello there',
                        'source' => [
                            'name' => 'Ewan McGregor',
                            'age' => 50,
                        ]
                    ],
                    [
                        'quote' => 'Hello there',
                        'source' => [
                            'name' => 'Ewan McGregor',
                            'age' => 52,
                        ]
                    ]
                ]
            ],
            $responseTwo,
        );
    }

    /** @test */
    public function it_calls_lifecycle_hooks_after_composing_a_response()
    {
        $closureOne = function (array $data) {
            $data['status'] = $data['status'] + 1;
            return $data;
        };

        self::assertEquals(
            [
                'status' => 201,
                'response' => [
                    'quote' => 'Hello there',
                    'source' => [
                        'name' => 'Ewan McGregor',
                        'age' => 32,
                    ]
                ]
            ],
            (new ComposedApiFactory)->afterComposing($closureOne)->make(),
        );

        $closureTwo = function (array $data) {
            $data['status'] = $data['status'] * 2;
            return $data;
        };

        self::assertEquals(
            [
                'status' => 402,
                'response' => [
                    'quote' => 'Hello there',
                    'source' => [
                        'name' => 'Ewan McGregor',
                        'age' => 32,
                    ]
                ]
            ],
            (new ComposedApiFactory)->afterComposing($closureOne)->afterComposing($closureTwo)->make(),
        );
    }

    /** @test */
    public function it_uses_data_resolvers()
    {
        $actual = (new FactoryWithResolver)->make();

        $this->assertEquals(
            ['first' => 'anna', 'duplicate' => 'anna'],
            $actual,
        );
    }

    /** @test */
    public function duplicate_resolves_with_custom_data()
    {
        $actual = (new FactoryWithResolver)->make([
            'first' => 'kevin',
        ]);

        $this->assertEquals(
            ['first' => 'kevin', 'duplicate' => 'kevin'],
            $actual,
        );
    }

    /** @test */
    public function factories_have_access_to_seedable_fakers()
    {
        $actual = (new FactoryUsesFaker)->seedFaker(11201)->make();

        $this->assertEquals(
            ['fake' => 'Melody Schaden'],
            $actual,
        );
    }

    /** @test */
    public function read_request_defaults_to_fallback_if_missing_request()
    {
        $actual = (new ReadsRequestFactory)->make();

        $this->assertEquals(
            ['name' => 'Ben Stiller', 'key' => 42],
            $actual,
        );
    }

    /** @test */
    public function read_request_gets_data_from_request()
    {
        $actual = (new ReadsRequestFactory)
            ->fromRequest(new SimplePerson)
            ->make();

        $this->assertEquals(
            ['name' => 'John Wick', 'key' => 42],
            $actual,
        );
    }

    /** @test */
    public function read_request_gets_data_from_request_with_overrides()
    {
        $actual = (new ReadsRequestFactory)
            ->fromRequest(new SimplePerson, ['name' => 'Matt Damon'])
            ->make();

        $this->assertEquals(
            ['name' => 'Matt Damon', 'key' => 42],
            $actual,
        );
    }
}
