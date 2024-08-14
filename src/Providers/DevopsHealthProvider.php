<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 11.08.2024
 * Time: 14:35
 */

namespace Devops\Health\Providers;

use App\Packages\Health\Checks\PlaceLastChangeDateCheck;
use App\Packages\Health\Checks\PlaceLastPriceDateCheck;
use App\Packages\Health\Checks\PlaceLastStockDateCheck;
use Devops\Health\Channels\DevopsHealthChannel;
use Devops\Health\Checks\QueueCheck;
use Devops\Health\Notifications\DevopsHealthNotification;
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
            PlaceLastPriceDateCheck::new()->maxHours(12),
            PlaceLastStockDateCheck::new()->maxHours(12),
            PlaceLastChangeDateCheck::new()->maxHours(12),

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
            PlaceLastPriceDateCheck::new()->maxHours(12),
            PlaceLastStockDateCheck::new()->maxHours(12),
            PlaceLastChangeDateCheck::new()->maxHours(12),
        ]);*/


        Notification::extend('devops_health', function ($app) {
            return new DevopsHealthChannel();
        });
    }
}
