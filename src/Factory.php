<?php

namespace Willow;

use Closure;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Willow\Fields\Resolver;

abstract class Factory
{
    protected Generator $faker;

    /**
     * Defines the data that should be returned when make is called.
     * @return array
     */
    abstract function definition(): array;

    public function __construct(
        protected ?int $count = null,
        protected ?Collection $afterMaking = null,
        protected ?Collection $afterComposing = null,
        protected ?RequestData $requestData = null,
        protected array $requestDataOverrides = [],
    ) {
        $this->afterMaking = $afterMaking ?: new Collection;
        $this->afterComposing = $afterComposing ?: new Collection;

        $this->faker = FakerFactory::create();
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
        if (!isset($this->count)) {
            return $this->composeResponses($this->makeSingleResponse($attributes));
        }

        if ($this->count < 1) {
            return $this->composeResponses([]);
        }

        return $this->composeResponses(array_map(function ($index) use ($attributes) {
            return $this->makeSingleResponse($attributes);
        }, range(1, $this->count)));
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
     * Composes the individual responses into one API response according to the users specifications.
     * @param array $responses
     *
     * @return array
     */
    private function composeResponses(array $responses): array
    {
        return $this->callAfterComposing($this->compose($responses));
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
        return (new Resolver)(array_replace_recursive($data, $overrides));
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
     * Allows user to define a factory that gets parts of it's response from a
     * request object.
     */
    protected function readRequest(string $requestKey, mixed $fallback): mixed
    {
        return data_get(
            $this->requestData?->make($this->requestDataOverrides)?->toArray(),
            $requestKey,
            $fallback,
        );
    }

    public function fromRequest(RequestData $request, array $overrides = []): self
    {
        return $this->newInstance([
            'requestData' => $request,
            'requestDataOverrides' => $overrides,
        ]);
    }

    public function seedFaker(int $seed): self
    {
        $this->faker->seed($seed);

        return $this;
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
            'requestData' => $this->requestData,
            'requestDataOverrides' => $this->requestDataOverrides,
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
        $this->afterMaking->each(function ($callable) use (&$results) {
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
        $this->afterComposing->each(function ($callable) use (&$results) {
            $results = $callable($results);
        });
        return $results;
    }
}
