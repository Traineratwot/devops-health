<?php
/**
 * Канал занимается отправкой уведомлений о состоянии здоровья приложения
 */

namespace Dimitriytiho\DevopsHealth\Channels;

use Dimitriytiho\DevopsHealth\Collections\ResultCollection;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Spatie\Health\Notifications\Notifiable;

class DevopsHealthChannel
{
    /**
     * @throws Exception
     */
    public function send(Notifiable $notifiable, Notification $notification)
    {
        $results = $notification->toHttp($notifiable);

        if (!$results instanceof ResultCollection) {
            throw new Exception('Notification must return ResultCollection');
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
                    Log::error($logMessage, $logData);
                    break;
                case 'ok':
                    Log::info($logMessage, $logData);
                    break;
                case 'warning':
                    Log::warning($logMessage, $logData);
                    break;
                default:
                    break;
            }
        });

        return true;
    }
}
