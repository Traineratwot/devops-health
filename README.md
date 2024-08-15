### Добавление канала

В файл ".env"

```dotenv
DEVOPS_SOURCE_TOKEN=TOKEN
LOG_CHANNEL=devopshealth
```

В файл "config/logging.php"

```php
<?php

return [  
    'channels' => [
    ########################################
        'devopshealth' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => \Dimitriytiho\DevopsHealth\Logging\Monolog\LogtailHandler::class,
            'handler_with' => [
                'sourceToken' => env('DEVOPS_SOURCE_TOKEN'),
            ],
        ],
    ########################################
    ],
];
```



### Включение Health по инструкции https://github.com/shuvroroy/filament-spatie-laravel-health

### Добавление канала в конфиг Health

В файл "config/health.php"

```php
return [
  'notifications' => [
  'enabled' => true,
    'notifications' => [
        \Dimitriytiho\DevopsHealth\Notifications\DevopsHealthNotification::class => ['devops_health'],
    ],
  ],
  'notifiable' => Spatie\Health\Notifications\Notifiable::class,
];
```
