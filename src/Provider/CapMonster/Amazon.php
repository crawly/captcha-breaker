<?php

namespace Crawly\CaptchaBreaker\Provider\CapMonster;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\ChallengeResponse;

class Amazon extends CapMonster implements ProviderInterface
{
    private $websiteURL;
    private $challengeScript;
    private $captchaScript;
    private $websiteKey;
    private $context;
    private $iv;
    private $cookieSolution;
    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $challengeScript,
        string $captchaScript,
        string $websiteKey,
        string $context,
        string $iv,
        bool $cookieSolution
    ) {
        $this->clientKey  = $clientKey;
        $this->websiteURL = $websiteURL;
        $this->challengeScript = $challengeScript;
        $this->captchaScript = $captchaScript;
        $this->websiteKey = $websiteKey;
        $this->context = $context;
        $this->iv = $iv;
        $this->cookieSolution = $cookieSolution;
        parent::__construct();
    }

    protected function getPostData(): array
    {
        return [
            'type' => 'AmazonTaskProxyless',
            'websiteURL' => $this->websiteURL,
            'challengeScript' => $this->challengeScript,
            'captchaScript' => $this->captchaScript,
            'websiteKey' => $this->websiteKey,
            'context' => $this->context,
            'iv' => $this->iv,
            'cookieSolution' => $this->cookieSolution,
        ];
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

        return new ChallengeResponse(
            $this->taskInfo->solution->cookies->{'aws-waf-token'}
        );
    }

    public function balance(): float
    {
        return $this->getBalance();
    }
}
