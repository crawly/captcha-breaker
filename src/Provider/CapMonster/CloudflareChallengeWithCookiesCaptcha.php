<?php

namespace Crawly\CaptchaBreaker\Provider\CapMonster;

use Crawly\CaptchaBreaker\Exception\DeprecatedMethod;
use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\CloudflareChallengeResponse;
use Psr\Log\LoggerInterface;

class CloudflareChallengeWithCookiesCaptcha extends CapMonster implements
    ProviderInterface
{
    protected $websiteURL;
    protected $websiteKey;
    protected $cloudflareTaskType;
    protected $htmlPageBase64;
    protected $userAgent;
    protected $logger;
    protected $proxyAddress;
    protected $proxyPort;
    protected $proxyLogin;
    protected $proxyPassword;
    protected $proxyType;

    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $websiteKey,
        string $htmlPageBase64,
        string $userAgent,
        string $proxyAddress,
        string $proxyPort,
        string $proxyLogin,
        string $proxyPassword,
        string $proxyType,
        LoggerInterface $logger = null
    ) {
        $this->clientKey = $clientKey;
        $this->websiteURL = $websiteURL;
        $this->websiteKey = $websiteKey;
        $this->htmlPageBase64 = $htmlPageBase64;
        $this->userAgent = $userAgent;
        $this->proxyAddress = $proxyAddress;
        $this->proxyPort = $proxyPort;
        $this->proxyLogin = $proxyLogin;
        $this->proxyPassword = $proxyPassword;
        $this->proxyType = $proxyType;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function getPostData(): array
    {
        return [
            "type" => "TurnstileTask",
            "websiteURL" => $this->websiteURL,
            "websiteKey" => $this->websiteKey,
            "cloudflareTaskType" => "cf_clearance",
            "htmlPageBase64" => $this->htmlPageBase64,
            "userAgent" => $this->userAgent,
            "proxyType" => $this->proxyType,
            "proxyAddress" => $this->proxyAddress,
            "proxyPort" => $this->proxyPort,
            "proxyLogin" => $this->proxyLogin,
            "proxyPassword" => $this->proxyPassword,
        ];
    }

    /**
     * {@inheritDoc}
     * @throws DeprecatedMethod
     */
    public function solve(): string
    {
        throw new DeprecatedMethod(
            'Method "solve" is deprecated and should not be used, use "resolveChallenge" instead.'
        );
    }

    public function resolveChallenge(): CloudflareChallengeResponse
    {
        $this->createTask();
        $this->waitForResult();

        return new CloudflareChallengeResponse(
            $this->taskInfo->solution->cf_clearance,
            $this->taskInfo->solution->token
        );
    }

    public function balance(): float
    {
        return $this->getBalance();
    }

    /**
     * Send complaint on an incorrectly solved captcha.
     *
     * @return bool
     */
    public function reportIncorrectCaptcha(): bool
    {
        return $this->reportIncorrect($this->getTaskId());
    }
}
