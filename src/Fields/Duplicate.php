<?php

namespace Willow\Fields;

class Duplicate implements FieldHelper
{
    public function __construct(
        protected string $fieldToDuplicate,
    ) {
    }

    public function resolve(array $madeFactory): mixed
    {
        return data_get($madeFactory, $this->fieldToDuplicate);
    }
}
