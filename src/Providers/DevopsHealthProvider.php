<?php

namespace Dimitriytiho\DevopsHealth\Providers;

use Dimitriytiho\DevopsHealth\Channels\DevopsHealthChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class DevopsHealthProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        Notification::extend('devops_health', function ($app) {
            return new DevopsHealthChannel();
        });
    }
}
