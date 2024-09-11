<?php

namespace Willow;

abstract class RequestData
{
    protected $data = [];

    public abstract function definition(): array;

    public function make(array $overrides = []): self
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
}