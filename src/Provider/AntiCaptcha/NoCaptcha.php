<?php


namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class NoCaptcha
 * @package Crawly\CaptchaBreaker\Provider\AntiCaptcha
 *
 * Class that manages all NoCaptcha challenges.
 */
class NoCaptcha extends AntiCaptcha implements ProviderInterface
{
    protected $websiteURL;
    protected $websiteKey;
    protected $websiteSToken;
    protected $proxyType;
    protected $proxyAddress;
    protected $proxyPort;
    protected $proxyLogin;
    protected $proxyPassword;
    protected $cookies;

    /**
     * NoCaptcha constructor.
     * @param string $clientKey
     * @param string $websiteURL
     * @param string $websiteKey
     * @param LoggerInterface|null $logger
     * @param string $proxyAddress
     * @param string $proxyPort
     * @param string $proxyLogin
     * @param string $proxyPassword
     * @param string $proxyType
     * @param string $cookies
     *
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
        string $proxyType = 'http',
        string $cookies = ''
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
        $this->cookies       = $cookies;

        parent::__construct();
    }

    protected function getPostData(): array
    {
        return [
            "type"          => empty($this->proxyAddress) ? "NoCaptchaTaskProxyless" : "NoCaptchaTask",
            "websiteURL"    => $this->websiteURL,
            "websiteKey"    => $this->websiteKey,
            "websiteSToken" => $this->websiteSToken,
            "proxyType"     => $this->proxyType,
            "proxyAddress"  => $this->proxyAddress,
            "proxyPort"     => $this->proxyPort,
            "proxyLogin"    => $this->proxyLogin,
            "proxyPassword" => $this->proxyPassword,
            "cookies"       => $this->cookies,
        ];
    }

    protected function getTaskSolution(): string
    {
        return $this->taskInfo->solution->gRecaptchaResponse;
    }

    /**
     * {@inheritDoc}
     */
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
