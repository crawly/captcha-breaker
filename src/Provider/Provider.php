<?php


namespace Crawly\CaptchaBreaker\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleRetry\GuzzleRetryMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class Provider
{
    const MAX_RETRIES = 10;
    const TIMEOUT     = 10;
    /**
     * @var LoggerInterface
     */
    protected $logger = null;
    protected $providerName = '';

    /**
     * @return CurlHandler
     * @codeCoverageIgnore
     */
    protected function getClientHandler()
    {
        return new CurlHandler();
    }

    protected function instanceClient(): void
    {
        $handler = $this->getClientHandler();

        $stack = HandlerStack::create($handler);
        $stack->push(GuzzleRetryMiddleware::factory());

        if ($this->logger != null) {
            $stack->push(
                Middleware::log(
                    $this->logger,
                    new MessageFormatter("{$this->providerName}: {uri} {code}")
                )
            );
        }

        $this->client = new Client([
            'headers'                  => [
                'Accept-Encoding'           => 'gzip, deflate',
                'Connection'                => 'keep-alive',
                'Accept-Charset'            => 'utf-8',
                'Upgrade-Insecure-Requests' => '1',
            ],
            'handler'                  => $stack,
            'cookies'                  => true,
            'allow_redirects'          => true,
            'timeout'                  => self::TIMEOUT,
            'max_retry_attempts'       => self::MAX_RETRIES,
            'retry_on_timeout'         => true,
            'retry_on_status'          => [429, 502, 503],
            'default_retry_multiplier' => $this->retryDelay(),
            'on_retry_callback'        => $this->logger != null ? $this->retryCallback() : null,
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getRetryDelaySeconds(): int
    {
        return 2000;
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedLocalVariable")
     */
    protected function retryDelay(): callable
    {
        return function ($numRequests, ?ResponseInterface $response): float {
            return (float)$this->getRetryDelaySeconds() / 1000;
        };
    }

    /**
     * @codeCoverageIgnore
     * @SuppressWarnings("PHPMD.UnusedLocalVariable")
     */
    protected function retryCallback(): callable
    {
        return function (
            int $attemptNumber,
            float $delay,
            RequestInterface &$request,
            array &$options,
            ?ResponseInterface $response
        ) {
            $this->logger->warning(sprintf(
                'CapMonster: Retrying %s %s %s/%s after %s seconds, %s',
                $request->getMethod(),
                $request->getUri(),
                $attemptNumber,
                self::MAX_RETRIES,
                $this->getRetryDelaySeconds() / 1000,
                ($response ? 'status code: ' . $response->getStatusCode() : null)
            ));
        };
    }
}
