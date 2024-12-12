<?php

namespace Dimitriytiho\DevopsHealth\Notifications;

use Dimitriytiho\DevopsHealth\Collections\ResultCollection;
use Exception;
use Spatie\Health\Notifications\CheckFailedNotification;

class DevopsHealthNotification extends CheckFailedNotification
{
    /**
     * @throws Exception
     */
    public function toHttp($notifiable)
    {
        $collection = new ResultCollection();

        foreach ($this->results as $result) {
            $collection->push($result);
        }

        return $collection;
    }

}
