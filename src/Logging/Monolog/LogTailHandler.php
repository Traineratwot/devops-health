<?php

declare(strict_types=1);

/*
 *
 * (c) Better Stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dimitriytiho\DevopsHealth\Logging\Monolog;

use Monolog\Handler\BufferHandler;
use Monolog\Level;
use Monolog\LogRecord;

/**
 */
class LogTailHandler extends BufferHandler
{
    const DEFAULT_BUBBLE = true;
    const DEFAULT_BUFFER_LIMIT = 1000;
    const DEFAULT_FLUSH_ON_OVERFLOW = true;
    const DEFAULT_FLUSH_INTERVAL_MILLISECONDS = 5000;

    private ?int $flushIntervalMs;
    private int|float|null $highResolutionTimeOfNextFlush;

    /**
     * @param  string            $sourceToken          LogTail source token
     * @param  string            $sourceUrl            LogTail URL
     * @param  int|string|Level  $level                The minimum logging level at which this handler will be triggered
     * @param  bool              $bubble               Whether the messages that are handled can bubble up the stack or not
     * @param  int               $bufferLimit          How many entries should be buffered at most, beyond that the oldest items are removed from the buffer
     * @param  bool              $flushOnOverflow      If true, the buffer is flushed when the max size has been reached, by default the oldest entries are
     *                                                 discarded
     * @param  int               $connectionTimeoutMs  The maximum time in milliseconds that you allow the connection phase to the server to take
     * @param  int               $timeoutMs            The maximum time in milliseconds that you allow a transfer operation to take
     * @param  int|null          $flushIntervalMs      The time in milliseconds after which next log record will trigger flushing all logs. Null to disable
     * @param  bool              $throwExceptions      Whether to throw exceptions when sending logs fails
     */
    public function __construct(
        string $sourceToken,
        string $sourceUrl,
        int|string|Level $level = Level::Debug,
        bool $bubble = self::DEFAULT_BUBBLE,
        int $bufferLimit = self::DEFAULT_BUFFER_LIMIT,
        bool $flushOnOverflow = self::DEFAULT_FLUSH_ON_OVERFLOW,
        int $connectionTimeoutMs = LogTailClient::DEFAULT_CONNECTION_TIMEOUT_MILLISECONDS,
        int $timeoutMs = LogTailClient::DEFAULT_TIMEOUT_MILLISECONDS,
        ?int $flushIntervalMs = self::DEFAULT_FLUSH_INTERVAL_MILLISECONDS,
        bool $throwExceptions = SynchronousLogTailHandler::DEFAULT_THROW_EXCEPTION
    ) {
        parent::__construct(
            new SynchronousLogTailHandler($sourceToken, $sourceUrl, $level, $bubble, $connectionTimeoutMs, $timeoutMs, $throwExceptions),
            $bufferLimit,
            $level,
            $bubble,
            $flushOnOverflow
        );
        $this->flushIntervalMs = $flushIntervalMs;
        $this->setHighResolutionTimeOfLastFlush();
    }

    private function setHighResolutionTimeOfLastFlush(): void
    {
        $currentHighResolutionTime = hrtime(true);
        if ($this->flushIntervalMs === null || $currentHighResolutionTime === false) {
            $this->highResolutionTimeOfNextFlush = null;

            return;
        }

        // hrtime(true) returns nanoseconds, converting flushIntervalMs from milliseconds to nanoseconds
        $this->highResolutionTimeOfNextFlush = $currentHighResolutionTime + $this->flushIntervalMs * 1e+6;
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        $return = parent::handle($record);

        if ($this->highResolutionTimeOfNextFlush !== null && $this->highResolutionTimeOfNextFlush <= hrtime(true)) {
            $this->flush();
        }

        return $return;
    }
    
    public function flush(): void
    {
        parent::flush();
        $this->setHighResolutionTimeOfLastFlush();
    }
}
