<?php

namespace Crawly\CaptchaBreaker\Provider\SolveCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\ChallengeResponse;
use Psr\Log\LoggerInterface;

class HCaptcha extends SolveCaptcha implements ProviderInterface
{
    protected $websiteURL;
    protected $websiteKey;
    protected $userAgent;
    protected $logger;

    /**
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $websiteKey,
        string $userAgent,
        LoggerInterface $logger = null,
    ) {
        $this->clientKey  = $clientKey;
        $this->websiteURL = $websiteURL;
        $this->websiteKey = $websiteKey;
        $this->userAgent  = $userAgent;

        $this->logger        = $logger;

        parent::__construct();
    }

    protected function getPostData()
    {
        return [
            'method' => 'hcaptcha',
            'sitekey' => $this->websiteKey,
            'pageurl' => $this->websiteURL,
        ];
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getTaskSolution(): string
    {
        return $this->taskInfo->request;
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
    public function balance()
    {
        return $this->getBalance();
    }

    /**
     * Send complaint on an Recaptcha
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function reportIncorrectCaptcha(): bool
    {
        return $this->reportIncorrect($this->getTaskId());
    }
}
