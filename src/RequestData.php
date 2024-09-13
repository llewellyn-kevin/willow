<?php

namespace Willow;

use Illuminate\Contracts\Support\Arrayable;
use Willow\Concerns\HasFaker;

abstract class RequestData implements Arrayable
{
    use HasFaker;

    protected $data = [];

    public function __construct()
    {
        $this->bootHasFaker();
    }

    public abstract function definition(): array;

    public function make(array $overrides = []): static
    {
        $this->data = $this->setOverrides($this->definition(), $overrides);
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function __get($name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __set($property, $value): void
    {
        $this->data[$property] = $value;
    }

    private function setOverrides(array $data, array $overrides): array
    {
        return array_replace_recursive($data, $overrides);
    }
}
