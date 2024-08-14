<?php
/**
 * Канал занимается отправкой уведомлений о состоянии здоровья приложения
 */

namespace Dimitriytiho\DevopsHealth\Channels;

use Illuminate\Notifications\Notification;

class DevopsHealthChannel
{
    public function send(\Spatie\Health\Notifications\Notifiable $notifiable, Notification $notification)
    {
        $results = $notification->toHttp($notifiable);

        if (!$results instanceof \Dimitriytiho\DevopsHealth\Collections\ResultCollection) {
            throw new \Exception('Notification must return ResultCollection');
        }


        $results->each(function ($logData) {
            $logMessage = sprintf(
                "[DEVOPS_HEALTH] Check: %s | Label: %s | Status: %s | Message: %s | Summary: %s | Ended at: %s",
                $logData['check'],
                $logData['label'],
                $logData['status'],
                $logData['message'],
                $logData['shortSummary'],
                $logData['ended_at']
            );

            switch ($logData['status']) {
                case 'failed':
                    \Illuminate\Support\Facades\Log::error($logMessage, $logData);
                    break;
                case 'ok':
                    \Illuminate\Support\Facades\Log::info($logMessage, $logData);
                    break;
                case 'warning':
                    \Illuminate\Support\Facades\Log::warning($logMessage, $logData);
                    break;
                default:
                    break;
            }
        });


        return true;
    }
}
