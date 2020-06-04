<?php


namespace Crawly\CaptchaBreaker\Provider\CapMonster;

use Crawly\CaptchaBreaker\Exception\SetupFailedException;
use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

class ReCaptchaV3 extends CapMonster implements ProviderInterface
{
    const MIN_SCORE_0_3 = 0.3;
    const MIN_SCORE_0_7 = 0.7;
    const MIN_SCORE_0_9 = 0.9;

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
            'type'       => 'RecaptchaV3TaskProxyless',
            'websiteURL' => $this->websiteURL,
            'websiteKey' => $this->websiteKey,
            'minScore'   => $this->minScore,
            'pageAction' => $this->pageAction,
        ];
    }

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    private function getTaskSolution()
    {
        return $this->taskInfo->solution->gRecaptchaResponse;
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function solve(): string
    {
        $this->createTask();
        $this->waitForResult();

        return $this->getTaskSolution();
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function balance(): float
    {
        return $this->getBalance();
    }
}
