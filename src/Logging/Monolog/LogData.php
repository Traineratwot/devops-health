<?php

namespace Dimitriytiho\DevopsHealth\Logging\Monolog;

use Illuminate\Support\Arr;
use Monolog\LogRecord;
use Throwable;

class LogData
{
    public ?string $message = null;
    public ?string $level_name = null;
    public ?int $level_code = null;
    public ?int $code = null;
    public mixed $context = null;
    public mixed $extra = null;
    public ?string $datetime = null;
    public ?string $file = null;
    public ?int $line = null;
    public mixed $trace = null;
    public ?string $channel = null;

    private function __construct(private readonly LogRecord $record)
    {
        $data = $record->toArray();
        $this->message = $record->message;
        $this->level_name = $record->level->name;
        $this->level_code = $record->level->value;
        $this->channel = $record->channel;
        $this->datetime = $record->datetime;
        $exception = Arr::get($data, 'context.exception');
        if ($exception instanceof Throwable) {
            $this->file = $exception->getFile();
            $this->line = $exception->getLine();
            $this->code = $exception->getCode();
            $trace = $exception->getTrace();
            $this->trace = $exception->getTrace();
        }
        $this->context = json_decode(json_encode($this->record->context));
        $this->extra = json_decode(json_encode($this->record->extra));
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

    public static function make(LogRecord $record)
    {
        return new self($record);
    }
}
