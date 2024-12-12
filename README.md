### Добавление канала

В файл ".env"

```dotenv
LOG_CHANNEL=devopshealth
LOG_SOURCE_TOKEN=TOKEN
LOG_SOURCE_URL=URL
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
            'handler' => \Dimitriytiho\DevopsHealth\Logging\Monolog\LogTailHandler::class,
            'handler_with' => [
                'sourceToken' => env('LOG_SOURCE_TOKEN'),
                'sourceUrl' => env('LOG_SOURCE_URL'),
            ],
        ],
    ########################################
    ],
];
```
