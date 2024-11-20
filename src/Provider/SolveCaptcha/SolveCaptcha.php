<?php

namespace Crawly\CaptchaBreaker\Provider\SolveCaptcha;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;
use Crawly\CaptchaBreaker\Provider\Provider;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

abstract class SolveCaptcha extends Provider
{
    private   $host   = 'api.solvecaptcha.com';
    private   $scheme = 'https';
    protected $clientKey;
    private   $taskId;
    protected $taskInfo;

    /**
     * @var Client
     */
    protected $client;

    /**
     * CapMonster constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->providerName = 'SolveCaptcha';
        $this->instanceClient();
    }

    abstract protected function getPostData();

    /**
     * Submit new task and receive tracking ID
     *
     * @throws TaskCreationFailedException
     */
    protected function createTask(): void
    {
        $postData = [
            'key' => $this->clientKey,
            'json' => 1,
            ...$this->getPostData(),
        ];

        $submitResult = $this->postRequest("in.php", $postData);

        if ($submitResult->status !== 1) {
            $this->log(
                "{$this->providerName} - API error {$submitResult->status}",
                LogLevel::ERROR
            );
            throw new TaskCreationFailedException($submitResult->status);
        }

        $this->taskId = $submitResult->request;
        $this->log("{$this->providerName} - created task with ID {$this->taskId}", LogLevel::INFO);
    }

    /**
     * @throws BreakFailedException
     */
    protected function waitForResult()
    {
        $postData = [
            'key' => $this->clientKey,
            'action' => 'get',
            'id' => $this->taskId,
            'json' => '1',
        ];

        $this->log("{$this->providerName} - waiting 3 seconds...", LogLevel::INFO);
        $this->sleep(3);

        for (;;) {
            $this->log("{$this->providerName} - requesting task status", LogLevel::INFO);
            $postResult = $this->getRequest('res.php', $postData);

            $this->taskInfo = $postResult;

            if ($this->taskInfo->status > 1) {
                $this->log(
                    "{$this->providerName} - API error {$this->taskInfo->status}: {$this->taskInfo->request}",
                    LogLevel::ERROR
                );

                throw new BreakFailedException($this->taskInfo->request);
            }

            if ($this->taskInfo->request === 'CAPCHA_NOT_READY') {
                $this->log("{$this->providerName} - task is still processing", LogLevel::INFO);
                $this->log("{$this->providerName} - waiting 1 second...", LogLevel::INFO);
                $this->sleep(1);
                continue;
            }

            break;
        }

        $this->log("{$this->providerName} - task is complete", LogLevel::INFO);
    }

    protected function getBalance(): float
    {
        $response = $this->getRequest('res.php', [
            'key' => $this->clientKey,
            'action' => 'getbalance',
            'json' => 1,
        ]);

        if ($response->errorId === 1) {
            return $response->request;
        }

        $this->log("{$this->providerName} - unknown API error", LogLevel::ERROR);
        throw new BalanceFailedException();
    }

    protected function reportIncorrect(int $taskId): bool
    {
        $response = $this->getRequest('res.php', [
            'key' => $this->clientKey,
            'action' => 'getbalance',
            'id' => $taskId,
            'json' => 1,
        ]);

        $this->log(
            "{$this->providerName} - " . ($response->status === 1 ? 'complaint accepted' : 'captcha not found or expired'),
            LogLevel::INFO
        );

        return $response->status === 1;
    }

    protected function postRequest($methodName, $postData)
    {
        $response = $this->client->post("{$this->scheme}://{$this->host}/{$methodName}", [
            'form_params' => $postData,
        ]);

        return json_decode($response->getBody()->getContents());
    }

    protected function getRequest($methodName, $queryParams)
    {
        $response = $this->client->get("{$this->scheme}://{$this->host}/{$methodName}", [
            'query' => $queryParams,
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param int $seconds
     * @codeCoverageIgnore
     */
    protected function sleep(int $seconds): void
    {
        sleep($seconds);
    }

    protected function log(string $message, string $logLevel): void
    {
        if ($this->logger != null) {
            $this->logger->log($logLevel, $message);
        }
    }

    protected function getTaskId()
    {
        return $this->taskId;
    }

    protected function getTaskInfo()
    {
        return $this->taskInfo;
    }
}
