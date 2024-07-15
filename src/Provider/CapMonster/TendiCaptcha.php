<?php

namespace Crawly\CaptchaBreaker\Provider\CapMonster;

use Crawly\CaptchaBreaker\Exception\DeprecatedMethod;
use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\TendiChallengeResponse;
use Psr\Log\LoggerInterface;

class TendiCaptcha extends CapMonster implements ProviderInterface
{
    private $websiteURL;
    private $websiteKey;

    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $websiteKey,
        LoggerInterface $logger = null
    ) {
        $this->clientKey  = $clientKey;
        $this->websiteURL = $websiteURL;
        $this->websiteKey = $websiteKey;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function getPostData(): array
    {
        return [
            'type'       => 'CustomTask',
            'class'      => 'TenDI',
            'websiteURL' => $this->websiteURL,
            'websiteKey' => $this->websiteKey,
        ];
    }

    /**
     * {@inheritDoc}
     * @throws DeprecatedMethod
     * @codeCoverageIgnore
     */
    public function solve(): string
    {
        throw new DeprecatedMethod(
            'Method "solve" is deprecated and should not be used, use "resolveChallenge" instead.'
        );
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function resolveChallenge(): TendiChallengeResponse
    {
        $this->createTask();
        $this->waitForResult();

        return new TendiChallengeResponse(
            $this->taskInfo->solution->data->ticket,
            $this->taskInfo->solution->data->randstr,
        );
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
     * Send complaint on an incorrectly solved captcha.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function reportIncorrectCaptcha(): bool
    {
        return $this->reportIncorrect($this->getTaskId());
    }
}
