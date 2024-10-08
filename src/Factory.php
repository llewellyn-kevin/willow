<?php

namespace Willow;

use Closure;
use Illuminate\Support\Collection;
use ReflectionFunction;
use Willow\Concerns\HasFaker;
use Willow\Exceptions\IncompatibleReturnValueException;
use Willow\Exceptions\InvalidArgumentsException;
use Willow\Fields\Resolver;

abstract class Factory
{
    use HasFaker;

    /** OVERRIDES */

    /**
     * Defines the data that should be returned when make is called.
     */
    abstract function definition(): array;

    /**
     * Determines how the API response should be composed after the data is
     * generated. Override to create response shells, status codes, etc.
     */
    public function compose(array $generated): array
    {
        return $generated;
    }

    /** INTERNAL */

    public function __construct(
        protected ?int $count = null,
        protected ?Collection $afterMaking = null,
        protected ?Collection $afterComposing = null,
        protected ?Collection $sequences = null,
        protected ?RequestData $requestData = null,
        protected array $requestDataOverrides = [],
    ) {
        $this->afterMaking = $afterMaking ?: new Collection;
        $this->afterComposing = $afterComposing ?: new Collection;
        $this->sequences = $sequences ?: new Collection;

        $this->bootHasFaker();
    }

    /**
     * Composes a fake API response using the user defined definition and returns
     * the result.
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
            return $this->makeSingleResponse($attributes, $index - 1);
        }, range(1, $this->count)));
    }

    /** USER FLUENT CONFIGURATION */

    /** Configures how many instances should be generated when response is made. */
    public function count(int $number): static
    {
        return $this->newInstance(['count' => $number]);
    }

    /** Add a callback for after each response object is generated. */
    public function afterMaking(Closure $callback): static
    {
        return $this->newInstance([
            'afterMaking' => $this->afterMaking->push($callback),
        ]);
    }

    /** Add a callback for after all the response objects are generated and composted. */
    public function afterComposing(Closure $callback): static
    {
        return $this->newInstance([
            'afterComposing' => $this->afterComposing->push($callback),
        ]);
    }

    /** Set overrides that will play in a sequence. */
    public function sequence(array|callable $sequence): static
    {
        if (is_array($sequence)) {
            $sequence = $this->arrayAsSequence($sequence);
        }

        return $this->newInstance([
            'sequences' => $this->sequences->push($sequence),
        ]);
    }

    /** Use a request data object to inform response generation. */
    public function fromRequest(RequestData $request, array $overrides = []): static
    {
        return $this->newInstance([
            'requestData' => $request,
            'requestDataOverrides' => $overrides,
        ]);
    }

    /** UTILITIES */

    protected function arrayAsSequence(array $array)
    {
        return function (int $index) use ($array) {
            $itemCount = count($array);
            return $array[$index % $itemCount];
        };
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

    /** IMPLEMENTATION DETAILS */

    private function newInstance(array $arguments): static
    {
        return new static(...array_values(array_merge([
            'count' => $this->count,
            'afterMaking' => $this->afterMaking,
            'afterComposing' => $this->afterComposing,
            'sequences' => $this->sequences,
            'requestData' => $this->requestData,
            'requestDataOverrides' => $this->requestDataOverrides,
        ], $arguments)));
    }

    private function makeSingleResponse(array $attributes, int $index = 0): array
    {
        $sequenceOverrides = [];
        $this->sequences->each(function (callable $sequence) use (&$sequenceOverrides, $index) {
            $reflection = new ReflectionFunction($sequence);
            $parameterCount = $reflection->getNumberOfRequiredParameters();
            if ($parameterCount == 0) {
                $sequenceResults = $sequence();
            } elseif ($parameterCount == 1) {
                $sequenceResults = $sequence($index);
            } else {
                throw new InvalidArgumentsException("Sequence callbacks must have 1 or fewer arguments.");
            }

            if (!is_array($sequenceResults)) {
                $type = gettype($sequenceResults);
                throw new IncompatibleReturnValueException("Sequence callbacks must return an 'array'. Got '$type'.");
            }

            $sequenceOverrides = array_replace_recursive($sequenceOverrides, $sequenceResults);
        });

        $attributes = array_replace_recursive($sequenceOverrides, $attributes);

        return $this->callAfterMaking(
            $this->setOverrides(
                $this->definition(),
                $attributes,
            ),
        );
    }

    private function composeResponses(array $responses): array
    {
        return $this->callAfterComposing($this->compose($responses));
    }

    private function setOverrides(array $data, array $overrides): array
    {
        return (new Resolver)(array_replace_recursive($data, $overrides));
    }

    private function callAfterMaking(array $results): array
    {
        $this->afterMaking->each(function ($callable) use (&$results) {
            $results = $callable($results);
        });
        return $results;
    }

    private function callAfterComposing(array $results): array
    {
        $this->afterComposing->each(function ($callable) use (&$results) {
            $results = $callable($results);
        });
        return $results;
    }
}
