<?php

namespace Dimitriytiho\DevopsHealth\Checks;

use Carbon\Carbon;
use Composer\InstalledVersions;
use Spatie\Health\Checks\Result;

class QueueCheck extends \Spatie\Health\Checks\Checks\QueueCheck
{
    public function run(): Result
    {
        $fails = [];

        foreach ($this->getQueues() as $queue) {
            $lastHeartbeatTimestamp = cache()->store($this->cacheStoreName)->get($this->getCacheKey($queue));

            if (!$lastHeartbeatTimestamp) {
                $fails[] = "Очередь `{$queue}` еще не запущена.";

                continue;
            }

            $latestHeartbeatAt = Carbon::createFromTimestamp($lastHeartbeatTimestamp);

            $carbonVersion = InstalledVersions::getVersion('nesbot/carbon');

            $minutesAgo = $latestHeartbeatAt->diffInMinutes();

            if (version_compare(
                $carbonVersion,
                '3.0.0',
                '<'
            )) {
                $minutesAgo += 1;
            }

            if ($minutesAgo > $this->failWhenTestJobTakesLongerThanMinutes) {
                $fails[] = "Последний запуск очереди `{$queue}` был более {$minutesAgo} минут назад.";
            }
        }

        $result = Result::make();

        if (!empty($fails)) {
            $result->meta($fails);

            return $result->failed('Выполнение заданий очереди не удалось. Проверьте мета для получения дополнительной информации.');
        }

        return $result->ok();
    }
}
