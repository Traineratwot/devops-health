<?php

/*
 *
 * (c) Better Stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dimitriytiho\DevopsHealth\Logging\Monolog;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;


class LogTailFormatter extends JsonFormatter
{

    public function __construct()
    {
        parent::__construct(self::BATCH_MODE_JSON, false);
        $this->setMaxNormalizeItemCount(PHP_INT_MAX);
    }

    public function format(LogRecord $record): string
    {
        $normalized = $this->normalize(self::formatRecord($record));

        return $this->toJson($normalized, true);
    }

    protected static function formatRecord(LogRecord $record): array
    {
        return [
            'dt' => $record->datetime,
            'message' => $record->message,
            'level' => $record->level->name,
            'monolog' => [
                'channel' => $record->channel,
                'context' => $record->context,
                'extra' => $record->extra,
            ],
        ];
    }

    public function formatBatch(array $records): string
    {
        $normalized = array_values($this->normalize(array_map(self::formatRecord(...), $records)));
        return $this->toJson($normalized, true);
    }
}
