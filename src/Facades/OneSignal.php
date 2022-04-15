<?php

namespace Shokme\OneSignal\Facades;

use Illuminate\Support\Facades\Facade;

class OneSignal extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Shokme\OneSignal\OneSignal::class;
    }
}
