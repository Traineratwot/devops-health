### Включение health

В файл "config/health.php"

```php
return [
  'notifications' => [
    'notifications' => [
        \Devops\Health\Notifications\DevopsHealthNotification::class => ['devops_health'],
    ],
  ],
  'notifiable' => Spatie\Health\Notifications\Notifiable::class,
];
```

### Добавление канала

В файл ".env"

```dotenv
DEVOPS_SOURCE_TOKEN="TOKEN"
LOG_CHANNEL=devopshealth
```

### Добавление канала

В файл "config/logging.php"

```php
<?php

return [  
    'channels' => [
    ########################################
        'devopshealth' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => \Devops\Health\Logging\Monolog\LogtailHandler::class,
            'handler_with' => [
                'sourceToken' => env('DEVOPS_SOURCE_TOKEN'),
            ],
        ],
    ########################################
    ],
];
```

