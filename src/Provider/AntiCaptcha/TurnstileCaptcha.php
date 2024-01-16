<?php


namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

class TurnstileCaptcha extends AntiCaptcha implements ProviderInterface
{
    protected $websiteURL;
    protected $websiteKey;
    protected $logger;
    protected $proxyAddress;
    protected $proxyPort;
    protected $proxyLogin;
    protected $proxyPassword;
    protected $proxyType;

    /**
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function __construct(
        string $clientKey,
        string $websiteURL,
        string $websiteKey,
        LoggerInterface $logger = null,
        string $proxyAddress = '',
        string $proxyPort = '',
        string $proxyLogin = '',
        string $proxyPassword = '',
        string $proxyType = 'http'
    ) {
        $this->clientKey  = $clientKey;
        $this->websiteURL = $websiteURL;
        $this->websiteKey = $websiteKey;

        $this->logger        = $logger;
        $this->proxyAddress  = $proxyAddress;
        $this->proxyPort     = $proxyPort;
        $this->proxyLogin    = $proxyLogin;
        $this->proxyPassword = $proxyPassword;
        $this->proxyType     = $proxyType;

        parent::__construct();
    }

    protected function getPostData()
    {
        return [
            'type'          => empty($this->proxyAddress) ? 'TurnstileTaskProxyless' : 'TurnstileTask',
            'websiteURL'    => $this->websiteURL,
            'websiteKey'    => $this->websiteKey,
            'proxyType'     => $this->proxyType,
            'proxyAddress'  => $this->proxyAddress,
            'proxyPort'     => $this->proxyPort,
            'proxyLogin'    => $this->proxyLogin,
            'proxyPassword' => $this->proxyPassword,
        ];
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
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
        $this->createTask();
        $this->waitForResult();

        return $this->getTaskSolution();
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
