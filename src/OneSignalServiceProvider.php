<?php

namespace Shokme\OneSignal;

use Illuminate\Support\ServiceProvider;

class OneSignalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/onesignal.php', 'onesignal');

        $this->app->bind(OneSignal::class, fn() => new OneSignal(config('onesignal.app_id'), config('onesignal.rest_api_key')));
    }

    public function boot()
    {

    }
}
