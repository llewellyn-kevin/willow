<?php

namespace Willow;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register()
    {
        App::singleton(Willow::getFacadeAccessor(), Dispatcher::class);
    }
}
