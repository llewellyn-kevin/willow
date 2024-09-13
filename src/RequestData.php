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

    private function setOverrides(array $data, array $overrides): array
    {
        return array_replace_recursive($data, $overrides);
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
