<?php


namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Exception\SetupFailedException;
use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

class ReCaptchaV3 extends AntiCaptcha implements ProviderInterface
{
    private $websiteURL;
    private $websiteKey;
    private $pageAction;
    private $minScore;

    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $websiteKey,
        string $pageAction,
        float $minScore,
        LoggerInterface $logger = null
    ) {
        $this->clientKey  = $clientKey;
        $this->websiteURL = $websiteURL;
        $this->websiteKey = $websiteKey;
        $this->pageAction = $pageAction;
        $this->minScore   = $minScore;

        $this->logger = $logger;

        parent::__construct();
    }

    protected function getPostData()
    {
        return [
            "type"       => "RecaptchaV3TaskProxyless",
            "websiteURL" => $this->websiteURL,
            "websiteKey" => $this->websiteKey,
            "minScore"   => $this->minScore,
            "pageAction" => $this->pageAction,
        ];
    }

    private function getTaskSolution()
    {
        return $this->taskInfo->solution->gRecaptchaResponse;
    }

    public function solve(): string
    {
        $this->createTask();
        $this->waitForResult();

        return $this->getTaskSolution();
    }

    /**
     * {@inheritDoc}
     */
    public function balance(): float
    {
        return $this->getBalance();
    }
}
