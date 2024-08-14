<?php

namespace Dimitriytiho\DevopsHealth\Notifications;

use Dimitriytiho\DevopsHealth\Collections\ResultCollection;

class DevopsHealthNotification extends \Spatie\Health\Notifications\CheckFailedNotification
{
    public function toHttp($notifiable)
    {
        $collection = new ResultCollection();

        foreach ($this->results as $result) {
            $collection->push($result);
        }

        return $collection;
    }

}
