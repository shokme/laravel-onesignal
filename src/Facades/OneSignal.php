<?php

namespace Shokme\OneSignal\Facades;

use Illuminate\Support\Facades\Facade;
use Shokme\OneSignal\Testing\Fakes\OneSignalFake;

class OneSignal extends Facade
{
    public static function fake()
    {
        static::swap(new OneSignalFake());
    }

    protected static function getFacadeAccessor(): string
    {
        return \Shokme\OneSignal\OneSignal::class;
    }
}
