<?php

namespace Willow;

use Illuminate\Support\Facades\Facade;

class Willow extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'willow';
    }
}
