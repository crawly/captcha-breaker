<?php

namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\ChallengeResponse;
use Psr\Log\LoggerInterface;

class Amazon extends AntiCaptcha implements ProviderInterface
{
    private $websiteURL;
    private $challengeScript;
    private $captchaScript;
    private $websiteKey;
    private $context;
    private $iv;

    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $challengeScript,
        string $captchaScript,
        string $websiteKey,
        string $context,
        string $iv,
        ?LoggerInterface $logger = null
    ) {
        $this->clientKey       = $clientKey;
        $this->websiteURL      = $websiteURL;
        $this->challengeScript = $challengeScript;
        $this->captchaScript   = $captchaScript;
        $this->websiteKey      = $websiteKey;
        $this->context         = $context;
        $this->iv              = $iv;
        $this->logger          = $logger;

        parent::__construct();
    }

    protected function getPostData()
    {
        return [
            'type'            => 'AmazonTaskProxyless',
            'websiteURL'      => $this->websiteURL,
            'challengeScript' => $this->challengeScript,
            'captchaScript'   => $this->captchaScript,
            'websiteKey'      => $this->websiteKey,
            'context'         => $this->context,
            'iv'              => $this->iv,
        ];
    }

    protected function getTaskSolution(): string
    {
        return $this->taskInfo->solution->token;
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
}
