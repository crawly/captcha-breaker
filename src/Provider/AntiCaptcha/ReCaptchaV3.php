<?php

namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\ChallengeResponse;
use Psr\Log\LoggerInterface;

class ReCaptchaV3 extends AntiCaptcha implements ProviderInterface
{
    const MIN_SCORE_0_3 = 0.3;
    const MIN_SCORE_0_7 = 0.7;
    const MIN_SCORE_0_9 = 0.9;

    private $websiteURL;
    private $websiteKey;
    private $pageAction;
    private $isEnterprise;
    private $minScore;

    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $websiteKey,
        string $pageAction,
        float $minScore,
        bool $isEnterprise = false,
        LoggerInterface $logger = null
    ) {
        $this->clientKey    = $clientKey;
        $this->websiteURL   = $websiteURL;
        $this->websiteKey   = $websiteKey;
        $this->pageAction   = $pageAction;
        $this->minScore     = $minScore;
        $this->isEnterprise = $isEnterprise;

        $this->logger = $logger;

        parent::__construct();
    }

    protected function getPostData()
    {
        return [
            'type'         => 'RecaptchaV3TaskProxyless',
            'websiteURL'   => $this->websiteURL,
            'websiteKey'   => $this->websiteKey,
            'minScore'     => $this->minScore,
            'pageAction'   => $this->pageAction,
            'isEnterprise' => $this->isEnterprise,
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
        return $this->resolveChallenge()
            ->getResult();
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function resolveChallenge(): ChallengeResponse
    {
        $this->createTask();
        $this->waitForResult();

        return new ChallengeResponse($this->getTaskSolution());
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function balance(): float
    {
        return $this->getBalance();
    }

    /**
     * Send complaint on an image captcha
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function reportIncorrectCaptcha(): bool
    {
        return $this->reportIncorrect($this->getTaskId());
    }
}
