<?php

namespace Crawly\CaptchaBreaker\Provider\CapSolver;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;
use Crawly\CaptchaBreaker\Provider\Provider;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

abstract class CapSolver extends Provider
{
    private   $host   = 'api.capsolver.com';
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
        $this->providerName = 'CapSolver';
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
            "clientKey" => $this->clientKey,
            "task"      => $this->getPostData(),
        ];

        $submitResult = $this->request("createTask", $postData);

        if ($submitResult->errorId != 0) {
            $this->log(
                "{$this->providerName} - API error {$submitResult->errorCode} : {$submitResult->errorDescription}",
                LogLevel::ERROR
            );
            throw new TaskCreationFailedException($submitResult->errorDescription);
        }

        $this->taskId = $submitResult->taskId;
        $this->log("{$this->providerName} - created task with ID {$this->taskId}", LogLevel::INFO);
    }

    /**
     * @throws BreakFailedException
     */
    protected function waitForResult()
    {
        $postData = [
            "clientKey" => $this->clientKey,
            "taskId"    => $this->taskId,
        ];

        $this->log("{$this->providerName} - waiting 3 seconds...", LogLevel::INFO);
        $this->sleep(3);

        for (;;) {
            $this->log("{$this->providerName} - requesting task status", LogLevel::INFO);
            $postResult = $this->request('getTaskResult', $postData);

            $this->taskInfo = $postResult;

            if ($this->taskInfo->errorId != 0) {
                $this->log(
                    "{$this->providerName} - API error {$this->taskInfo->errorCode} : {$this->taskInfo->errorDescription}",
                    LogLevel::ERROR
                );
                throw new BreakFailedException($this->taskInfo->errorDescription);
            }
            if ($this->taskInfo->status == 'processing') {
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
        $postData = [
            'clientKey' => $this->clientKey,
        ];

        $response = $this->request('getBalance', $postData);

        if ($response->errorId == 0) {
            return $response->balance;
        }

        $this->log("{$this->providerName} - unknown API error", LogLevel::ERROR);
        throw new BalanceFailedException();
    }

    protected function reportIncorrect(int $taskId): bool
    {
        $postData = [
            'clientKey' => $this->clientKey,
            'taskId'    => $taskId,
        ];

        $type             = $this->getPostData()['type'];
        $reportMethodName = '';

        if (in_array($type, ['HCaptchaTaskProxyless', 'HCaptchaTask'])) {
            $reportMethodName = 'reportIncorrectHcaptcha';
        } else if (in_array($type, ['NoCaptchaTaskProxyless', 'NoCaptchaTask', 'RecaptchaV3TaskProxyless'])) {
            $reportMethodName = 'reportIncorrectRecaptcha';
        } else if ($type == 'ImageToTextTask') {
            $reportMethodName = 'reportIncorrectImageCaptcha';
        }

        if (empty($reportMethodName)) {
            return false;
        }

        $response = $this->request($reportMethodName, $postData);

        $this->log(
            "{$this->providerName} - " . ($response->errorId == 0 ? 'complaint accepted' : 'captcha not found or expired'),
            LogLevel::INFO
        );

        return $response->errorId == 0;
    }

    protected function request($methodName, $postData)
    {
        $response = $this->client->post("{$this->scheme}://{$this->host}/{$methodName}", [
            'json' => $postData,
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
