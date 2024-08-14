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
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(RunHealthChecksCommand::class)->everyFiveMinutes();
            $schedule->command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
            $schedule->command(DispatchQueueCheckJobsCommand::class)->everyMinute();
        });


        Health::checks([
            QueueCheck::new(),

            ScheduleCheck::new()->heartbeatMaxAgeInMinutes(5),
            EnvironmentCheck::new(),
            //OptimizedAppCheck::new(),
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new(),
            DatabaseSizeCheck::new()->failWhenSizeAboveGb(errorThresholdGb: 5.0),
            DebugModeCheck::new(),
//            \App\Packages\Health\Checks\PlaceLastPriceDateCheck::new()->maxHours(12),
//            \App\Packages\Health\Checks\PlaceLastStockDateCheck::new()->maxHours(12),
//            \App\Packages\Health\Checks\PlaceLastChangeDateCheck::new()->maxHours(12),

        ]);

        /*config()->set('health.notifications.notifications', [
            DevopsHealthNotification::class => ['devops_health'],
        ]);
        config()->set('health.notifications.notifiable', \Spatie\Health\Notifications\Notifiable::class);*/
        /*Health::checks([
            QueueCheck::new(),
            ScheduleCheck::new()->heartbeatMaxAgeInMinutes(5),
            EnvironmentCheck::new(),
            //OptimizedAppCheck::new(),
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new(),
            DatabaseSizeCheck::new()->failWhenSizeAboveGb(errorThresholdGb: 5.0),
            DebugModeCheck::new(),
            \App\Packages\Health\Checks\PlaceLastPriceDateCheck::new()->maxHours(12),
            \App\Packages\Health\Checks\PlaceLastStockDateCheck::new()->maxHours(12),
            \App\Packages\Health\Checks\PlaceLastChangeDateCheck::new()->maxHours(12),
        ]);*/


        Notification::extend('devops_health', function ($app) {
            return new DevopsHealthChannel();
        });
    }
}
