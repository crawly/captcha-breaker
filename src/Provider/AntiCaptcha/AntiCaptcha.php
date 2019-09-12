<?php


namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class AntiCaptcha
{
    private $host = 'api.anti-captcha.com';
    private $scheme = 'https';
    protected $clientKey;
    private $taskId;
    protected $taskInfo;
    /**
     * @var LoggerInterface
     */
    protected $logger = null;
    /**
     * @var Client
     */
    protected $client;

    /**
     * AntiCaptcha constructor.
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
                "AntiCaptcha - API error {$submitResult->errorCode} : {$submitResult->errorDescription}",
                LogLevel::ERROR
            );
            throw new TaskCreationFailedException($submitResult->errorDescription);
        }

        $this->taskId = $submitResult->taskId;
        $this->log("AntiCaptcha - created task with ID {$this->taskId}", LogLevel::INFO);
    }

    /**
     * @param int $currentSecond
     * @throws BreakFailedException
     */
    protected function waitForResult($currentSecond = 0)
    {
        $postData = [
            "clientKey" => $this->clientKey,
            "taskId"    => $this->taskId,
        ];

        if ($currentSecond == 0) {
            $this->log('AntiCaptcha - waiting 3 seconds...', LogLevel::INFO);
            $this->sleep(3);
        } else {
            $this->log('AntiCaptcha - waiting 1 second...', LogLevel::INFO);
            $this->sleep(1);
        }

        $this->log('AntiCaptcha - requesting task status', LogLevel::INFO);
        $postResult = $this->request('getTaskResult', $postData);

        $this->taskInfo = $postResult;

        if ($this->taskInfo->errorId != 0) {
            $this->log(
                "AntiCaptcha - API error {$this->taskInfo->errorCode} : {$this->taskInfo->errorDescription}",
                LogLevel::ERROR
            );
            throw new BreakFailedException($this->taskInfo->errorDescription);
        }
        if ($this->taskInfo->status == 'processing') {
            $this->log('AntiCaptcha - task is still processing', LogLevel::INFO);

            return $this->waitForResult($currentSecond + 1);
        }

        $this->log('AntiCaptcha - task is complete', LogLevel::INFO);
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

        $this->log('AntiCaptcha - unknown API error', LogLevel::ERROR);
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
            'AntiCaptcha - ' . ($response->errorId == 0 ? 'complaint accepted' : 'captcha not found or expired'),
            LogLevel::INFO
        );

        return $response->errorId == 0;
    }

    protected function request($methodName, $postData)
    {
        $response = $this->client->post($methodName, [
            'json' => $postData,
        ]);

        return json_decode($response->getBody()->getContents());
    }

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

        if ($this->logger != null) {
            $stack->push(
                Middleware::log(
                    $this->logger,
                    new MessageFormatter('AntiCaptcha: {uri} {code}')
                )
            );
        }

        $this->client = new Client([
            'base_uri'        => "{$this->scheme}://{$this->host}/",
            'headers'         => [
                'Accept-Encoding'           => 'gzip, deflate',
                'Connection'                => 'keep-alive',
                'Accept-Charset'            => 'utf-8',
                'Upgrade-Insecure-Requests' => '1',
            ],
            'handler'         => $stack,
            'cookies'         => true,
            'allow_redirects' => true,
            'timeout'         => 30,
        ]);
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
