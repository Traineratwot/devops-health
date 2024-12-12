<?php

namespace Dimitriytiho\DevopsHealth\Logging\Monolog;

use CurlHandle;
use LogicException;
use RuntimeException;

use function curl_init;
use function curl_setopt;
use function extension_loaded;
use function in_array;

class LogTailClient
{
    const DEFAULT_CONNECTION_TIMEOUT_MILLISECONDS = 5000;
    const DEFAULT_TIMEOUT_MILLISECONDS = 5000;
    private static array $errorCodes = [
        CURLE_COULDNT_RESOLVE_HOST,
        CURLE_COULDNT_CONNECT,
        CURLE_HTTP_NOT_FOUND,
        CURLE_READ_ERROR,
        CURLE_OPERATION_TIMEOUTED,
        CURLE_HTTP_POST_ERROR,
        CURLE_SSL_CONNECT_ERROR,
    ];
    private string $sourceToken;
    private string $url;
    private CurlHandle $handle;
    private int $connectionTimeoutMs;
    private int $timeoutMs;

    public function __construct(
        $sourceToken,
        $url,
        int $connectionTimeoutMs = self::DEFAULT_CONNECTION_TIMEOUT_MILLISECONDS,
        int $timeoutMs = self::DEFAULT_TIMEOUT_MILLISECONDS,
    ) {
        if (!extension_loaded('curl')) {
            throw new LogicException('The curl extension is needed to use the LogTailHandler');
        }

        $this->sourceToken = $sourceToken;
        $this->url = $url;
        $this->connectionTimeoutMs = $connectionTimeoutMs;
        $this->timeoutMs = $timeoutMs;
    }

    public function send(array $data): void
    {
        if (!isset($this->handle)) {
            $this->initCurlHandle();
        }

        curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);

        self::execute($this->handle, 5, false);
    }

    private function initCurlHandle(): void
    {
        $this->handle = curl_init();

        $headers = [
            'Content-Type: application/json',
            "Token: {$this->sourceToken}",
        ];

        curl_setopt($this->handle, CURLOPT_URL, $this->url);
        curl_setopt($this->handle, CURLOPT_POST, true);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT_MS, $this->connectionTimeoutMs);
        curl_setopt($this->handle, CURLOPT_TIMEOUT_MS, $this->timeoutMs);
    }

    /**
     * Executes a CURL request with optional retries and exception on failure
     *
     * @param  CurlHandle  $ch  curl handler
     * @return bool|string @see curl_exec
     */
    public static function execute(CurlHandle $ch, int $retries = 5, bool $closeAfterDone = true)
    {
        while ($retries--) {
            $curlResponse = curl_exec($ch);
            if ($curlResponse === false) {
                $curlErrno = curl_errno($ch);

                if (false === in_array($curlErrno, self::$errorCodes, true) || $retries === 0) {
                    $curlError = curl_error($ch);

                    if ($closeAfterDone) {
                        curl_close($ch);
                    }

                    throw new RuntimeException(sprintf('Curl error (code %d): %s', $curlErrno, $curlError));
                }

                continue;
            }

            if ($closeAfterDone) {
                curl_close($ch);
            }

            return $curlResponse;
        }

        return false;
    }
}
