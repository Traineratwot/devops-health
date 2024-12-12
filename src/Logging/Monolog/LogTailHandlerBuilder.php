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

use Monolog\Level;

final class LogTailHandlerBuilder
{
    protected bool $throwExceptions = SynchronousLogTailHandler::DEFAULT_THROW_EXCEPTION;
    private string $sourceToken;
    private string $url;
    private Level $level = Level::Debug;
    private bool $bubble = LogTailHandler::DEFAULT_BUBBLE;
    private int $bufferLimit = LogTailHandler::DEFAULT_BUFFER_LIMIT;
    private bool $flushOnOverflow = LogTailHandler::DEFAULT_FLUSH_ON_OVERFLOW;
    private int $connectionTimeoutMs = LogTailClient::DEFAULT_CONNECTION_TIMEOUT_MILLISECONDS;
    private int $timeoutMs = LogTailClient::DEFAULT_TIMEOUT_MILLISECONDS;
    private ?int $flushIntervalMs = LogTailHandler::DEFAULT_FLUSH_INTERVAL_MILLISECONDS;

    /**
     * @internal use {@see self::withSourceToken()} instead
     */
    private function __construct(string $sourceToken, string $url)
    {
        $this->sourceToken = $sourceToken;
        $this->url = $url;
    }

    /**
     * Builder for comfortable creation of {@see LogTailHandler}.
     *
     * @return self   Always returns new immutable instance
     * @see    https://logs.betterstack.com/team/0/sources
     * @var    string $sourceToken Your Better Stack source token.
     */
    public static function withSourceToken(string $sourceToken, string $url): self
    {
        return new self($sourceToken, $url);
    }

    /**
     * Sets the minimum logging level at which this handler will be triggered.
     *
     * @param  Level  $level
     * @return self  Always returns new immutable instance
     */
    public function withLevel(Level $level): self
    {
        $clone = clone $this;
        $clone->level = $level;

        return $clone;
    }

    /**
     * Sets whether the messages that are handled can bubble up the stack or not.
     *
     * @param  bool  $bubble
     * @return self Always returns new immutable instance
     */
    public function withLogBubbling(bool $bubble): self
    {
        $clone = clone $this;
        $clone->bubble = $bubble;

        return $clone;
    }

    /**
     * Sets how many entries should be buffered at most, beyond that the oldest items are flushed or removed from the buffer.
     *
     * @param  int  $bufferLimit
     * @return self Always returns new immutable instance
     */
    public function withBufferLimit(int $bufferLimit): self
    {
        $clone = clone $this;
        $clone->bufferLimit = $bufferLimit;

        return $clone;
    }

    /**
     * Sets whether the buffer is flushed (true) or discarded (false) when the max size has been reached.
     *
     * @param  bool  $flushOnOverflow
     * @return self Always returns new immutable instance
     */
    public function withFlushOnOverflow(bool $flushOnOverflow): self
    {
        $clone = clone $this;
        $clone->flushOnOverflow = $flushOnOverflow;

        return $clone;
    }

    /**
     * Sets the maximum time in milliseconds that you allow the connection phase to the server to take.
     *
     * @param  int  $connectionTimeoutMs
     * @return self Always returns new immutable instance
     */
    public function withConnectionTimeoutMilliseconds(int $connectionTimeoutMs): self
    {
        $clone = clone $this;
        $clone->connectionTimeoutMs = $connectionTimeoutMs;

        return $clone;
    }

    /**
     * Sets the maximum time in milliseconds that you allow a transfer operation to take.
     *
     * @param  int  $timeoutMs
     * @return self Always returns new immutable instance
     */
    public function withTimeoutMilliseconds(int $timeoutMs): self
    {
        $clone = clone $this;
        $clone->timeoutMs = $timeoutMs;

        return $clone;
    }

    /**
     * Set the time in milliseconds after which next log record will trigger flushing all logs. Null to disable.
     *
     * @param  int|null  $flushIntervalMs
     * @return self     Always returns new immutable instance
     */
    public function withFlushIntervalMilliseconds(?int $flushIntervalMs): self
    {
        $clone = clone $this;
        $clone->flushIntervalMs = $flushIntervalMs;

        return $clone;
    }

    /**
     * Sets whether to throw exceptions when sending logs fails.
     *
     * @param  bool  $throwExceptions
     * @return self Always returns new immutable instance
     */
    public function withExceptionThrowing(bool $throwExceptions): self
    {
        $clone = clone $this;
        $clone->throwExceptions = $throwExceptions;

        return $clone;
    }

    /**
     * Builds the {@see LogTailHandler} instance based on the setting.
     *
     * @return LogTailHandler
     */
    public function build(): LogTailHandler
    {
        return new LogTailHandler(
            $this->sourceToken,
            $this->url,
            $this->level,
            $this->bubble,
            $this->bufferLimit,
            $this->flushOnOverflow,
            $this->connectionTimeoutMs,
            $this->timeoutMs,
            $this->flushIntervalMs
        );
    }
}
