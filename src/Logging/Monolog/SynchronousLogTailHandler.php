<?php

declare(strict_types=1);

namespace Dimitriytiho\DevopsHealth\Logging\Monolog;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Throwable;

/**
 * Sends log to LogTail.
 */
class SynchronousLogTailHandler extends AbstractProcessingHandler
{
    const DEFAULT_THROW_EXCEPTION = false;

    private LogTailClient $client;
    private string $sourceToken;
    private bool $throwExceptions;

    /**
     * @param  string            $sourceToken
     * @param  int|string|Level  $level
     * @param  bool              $bubble
     * @param  string            $url
     * @param  int               $connectionTimeoutMs
     * @param  int               $timeoutMs
     * @param  bool              $throwExceptions
     */
    public function __construct(
        string $sourceToken,
        string $url,
        int|string|Level $level = Level::Debug,
        bool $bubble = LogTailHandler::DEFAULT_BUBBLE,
        int $connectionTimeoutMs = LogTailClient::DEFAULT_CONNECTION_TIMEOUT_MILLISECONDS,
        int $timeoutMs = LogTailClient::DEFAULT_TIMEOUT_MILLISECONDS,
        bool $throwExceptions = self::DEFAULT_THROW_EXCEPTION
    ) {
        parent::__construct($level, $bubble);

        $this->sourceToken = $sourceToken;
        $this->client = new LogTailClient($this->sourceToken, $url, $connectionTimeoutMs, $timeoutMs);
        $this->throwExceptions = $throwExceptions;

        $this->pushProcessor(new IntrospectionProcessor($level, ['Logtail\\']));
        $this->pushProcessor(new WebProcessor);
        $this->pushProcessor(new ProcessIdProcessor);
        $this->pushProcessor(new HostnameProcessor);
    }

    /**
     * @param  array  $records
     * @return void
     * @throws Throwable
     */
    public function handleBatch(array $records): void
    {
        try {
            //$formattedRecords = $this->getFormatter()->formatBatch($records);
            $formattedRecords = [];
            foreach ($records as $record) {
                $formattedRecords[] = LogData::make($record)->toArray();
            }
            $this->client->send($formattedRecords);
        } catch (Throwable $throwable) {
            if ($this->throwExceptions) {
                throw $throwable;
            } else {
                trigger_error('Failed to send '.count($records).' log records to Better Stack because of '.$throwable, E_USER_WARNING);
            }
        }
    }

    public function getFormatter(): FormatterInterface
    {
        return $this->getDefaultFormatter();
    }

    /**
     * @return LogTailFormatter
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LogTailFormatter();
    }

    /**
     * @param  LogRecord  $record
     * @return void
     * @throws Throwable
     */
    protected function write(LogRecord $record): void
    {
        try {
            $this->client->send(LogData::make($record)->toArray());
        } catch (Throwable $throwable) {
            if ($this->throwExceptions) {
                throw $throwable;
            } else {
                trigger_error('Failed to send a single log record to Better Stack because of '.$throwable, E_USER_WARNING);
            }
        }
    }
}
