<?php

namespace Willow\Concerns;

use Faker\Factory as FakerFactory;
use Faker\Generator;

trait HasFaker
{
    protected Generator $faker;

    public function bootHasFaker(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function seedFaker(int $seed): static
    {
        $this->faker->seed($seed);

        return $this;
    }
}
