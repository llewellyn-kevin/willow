<?php

namespace Willow;

abstract class Factory
{

    /**
     * Defines the data that should be returned when make is called.
     * @return array
     */
    abstract function definition(): array;

    /**
     * Determines how the API response should be composed after the data is
     * generated.
     * @param array $generated
     *
     * @return array
     */
    public function compose(array $generated): array
    {
        return $generated;
    }

    /**
     * Composes a fake API response using the user defined definition and returns the result.
     * @return array
     */
    public function make(): array
    {
        return $this->compose($this->definition());
    }
}
