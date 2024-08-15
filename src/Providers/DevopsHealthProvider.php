<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 11.08.2024
 * Time: 14:35
 */

namespace Dimitriytiho\DevopsHealth\Providers;

use Dimitriytiho\DevopsHealth\Channels\DevopsHealthChannel;
use Dimitriytiho\DevopsHealth\Checks\QueueCheck;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Spatie\Health\Facades\Health;
use Illuminate\Support\Facades\Notification;

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
