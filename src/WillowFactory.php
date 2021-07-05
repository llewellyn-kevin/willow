<?php

namespace Willow;

abstract class WillowFactory
{
    /**
     * Defines the data that should be returned when make is called.
     * @return array
     */
    abstract function definition(): array;

    /**
     * Composes a fake API response using the user defined definition and returns the result.
     * @return array
     */
    public function make(): array
    {
        return $this->definition();
    }
}
