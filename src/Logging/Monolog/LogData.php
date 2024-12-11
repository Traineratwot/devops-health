<?php

namespace Dimitriytiho\DevopsHealth\Logging\Monolog;

use Illuminate\Support\Arr;
use Monolog\LogRecord;

class LogData
{
    public $message;
    public $level_name;
    public $level_code;
    public $code;
    public $context;
    public $extra;
    public $datetime;
    public $file;
    public $line;
    public $trace;
    public $channel;

    public static function make(LogRecord $record)
    {
        return new self($record);
    }

    private function __construct(private LogRecord $record)
    {
        $data = $record->toArray();
        $this->message = Arr::get($data, 'message');
        $this->level_name = Arr::get($data, 'level_name');
        $this->level_code = Arr::get($data, 'level');
        $this->channel = Arr::get($data, 'channel');
        $this->datetime = Arr::get($data, 'datetime');
        $this->file = Arr::get($data, 'context.exception')?->file;
        $this->line = Arr::get($data, 'context.exception')?->line;
        $this->code = Arr::get($data, 'context.exception')?->code;
        $this->context();
        $this->trace();
    }

    private function context()
    {
        $this->context = serialize($this->record->context);
    }

    private function trace()
    {
        $this->extra = serialize($this->record->extra);
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'level_name' => $this->level_name,
            'level_code' => $this->level_code,
            'code' => $this->code,
            'context' => $this->context,
            'extra' => $this->extra,
            'datetime' => $this->datetime,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->trace,
            'channel' => $this->channel,
        ];
    }
}
