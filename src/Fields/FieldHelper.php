<?php

namespace Willow\Fields;

interface FieldHelper
{
    public function resolve(array $madeFactory): mixed;
}
