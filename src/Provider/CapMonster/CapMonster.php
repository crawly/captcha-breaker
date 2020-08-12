<?php


namespace Crawly\CaptchaBreaker\Provider\CapMonster;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;
use Crawly\CaptchaBreaker\Provider\Provider;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

abstract class CapMonster extends Provider
{
    private $host   = 'api.capmonster.cloud';
    private $scheme = 'https';
    protected $clientKey;
    private $taskId;
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
                "CapMonster - API error {$submitResult->errorCode} : {$submitResult->errorDescription}",
                LogLevel::ERROR
            );
            throw new TaskCreationFailedException($submitResult->errorDescription);
        }

        $this->taskId = $submitResult->taskId;
        $this->log("CapMonster - created task with ID {$this->taskId}", LogLevel::INFO);
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

        $this->log('CapMonster - waiting 3 seconds...', LogLevel::INFO);
        $this->sleep(3);

        for (; ;) {
            $this->log('CapMonster - requesting task status', LogLevel::INFO);
            $postResult = $this->request('getTaskResult', $postData);

            $this->taskInfo = $postResult;

            if ($this->taskInfo->errorId != 0) {
                $this->log(
                    "CapMonster - API error {$this->taskInfo->errorCode} : {$this->taskInfo->errorDescription}",
                    LogLevel::ERROR
                );
                throw new BreakFailedException($this->taskInfo->errorDescription);
            }
            if ($this->taskInfo->status == 'processing') {
                $this->log('CapMonster - task is still processing', LogLevel::INFO);
                $this->log('CapMonster - waiting 1 second...', LogLevel::INFO);
                $this->sleep(1);
                continue;
            }

            break;
        }

        $this->log('CapMonster - task is complete', LogLevel::INFO);
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

        $this->log('CapMonster - unknown API error', LogLevel::ERROR);
        throw new BalanceFailedException();
    }

    protected function reportIncorrect(int $taskId, bool $image): bool
    {
        $postData = [
            'clientKey' => $this->clientKey,
            'taskId'    => $taskId,
        ];

        $response = $this->request($image ? 'reportIncorrectImageCaptcha' : 'reportIncorrectRecaptcha', $postData);

        $this->log(
            'CapMonster - ' . ($response->errorId == 0 ? 'complaint accepted' : 'captcha not found or expired'),
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
