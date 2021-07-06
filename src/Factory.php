<?php

namespace Willow;

use Closure;
use Illuminate\Database\Eloquent\Collection;

abstract class Factory
{
    /**
     * The number of records that should be generated.
     * @var int
     */
    public ?int $count;

    /**
     * A set of callables that will be invoked after an instance is created.
     * @var Collection
     */
    protected Collection $afterMaking;

    /**
     * A set of callables that will be invoked after all instances are created
     * and the full response is generated.
     * @var Collection
     */
    protected Collection $afterComposing;

    /**
     * Defines the data that should be returned when make is called.
     * @return array
     */
    abstract function definition(): array;

    public function __construct(int $count = null,
                                Collection $afterMaking = null,
                                Collection $afterComposing = null)
    {
        $this->count = $count;
        $this->afterMaking = $afterMaking ?: new Collection;
        $this->afterComposing = $afterComposing ?: new Collection;
    }


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
    public function make(array $attributes = []): array
    {
        if(!isset($this->count)) {
            return $this->callAfterComposing(
                $this->compose($this->makeSingleResponse($attributes)));
        }

        if($this->count < 1) {
            return $this->callAfterComposing($this->compose([]));
        }

        return $this->callAfterComposing($this->compose(array_map(function($index) use ($attributes) {
            return $this->makeSingleResponse($attributes);
        }, range(1, $this->count))));
    }

    /**
     * Creates some data from the provided definition and attribute overrides.
     * @param array $attributes
     *
     * @return array
     */
    private function makeSingleResponse(array $attributes): array
    {
        return $this->callAfterMaking($this->setOverrides($this->definition(), $attributes));
    }

    /**
     * Takes overrides provided by the consumer of the factory and applies
     * them to the generated data.
     * @param array $data
     * @param array $overrides
     *
     * @return array
     */
    private function setOverrides(array $data, array $overrides): array
    {
        foreach($overrides as $key => $value) {
            data_set($data, $key, $value);
        }
        return $data;
    }

    /**
     * Configures how many instances should be generated when response is made.
     * @param int $number
     */
    public function count(int $number)
    {
        return $this->newInstance(['count' => $number]);
    }

    /**
     * Add a callback for after each response object is generated.
     * @param Closure $callback
     *
     * @return Factory
     */
    public function afterMaking(Closure $callback): Factory
    {
        return $this->newInstance(['afterMaking' => $this->afterMaking->push($callback)]);
    }

    /**
     * Add a callback for after all the response objects are generated and composted.
     * @param Closure $callback
     *
     * @return Factory
     */
    public function afterComposing(Closure $callback): Factory
    {
        return $this->newInstance(['afterComposing' => $this->afterComposing->push($callback)]);
    }

    /**
     * Generates an instance of the factory with the given arguments.
     * @param array $arguments
     *
     * @return Factory
     */
    protected function newInstance(array $arguments): Factory
    {
        return new static(...array_values(array_merge([
            'count' => $this->count,
            'afterMaking' => $this->afterMaking,
            'afterComposing' => $this->afterComposing,
        ], $arguments)));
    }

    /**
     * Invokes all of the callables provided by the user for this lifecycle hook.
     * @param array $results
     *
     * @return array
     */
    protected function callAfterMaking(array $results): array
    {
        $this->afterMaking->each(function($callable) use (&$results) {
            $results = $callable($results);
        });
        return $results;
    }

    /**
     * Invokes all of the callables provided by the user for this lifecycle hook.
     * @param array $results
     *
     * @return array
     */
    protected function callAfterComposing(array $results): array
    {
        $this->afterComposing->each(function($callable) use (&$results) {
            $results = $callable($results);
        });
        return $results;
    }
}
