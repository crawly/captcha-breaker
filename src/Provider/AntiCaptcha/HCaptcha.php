<?php


namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

class HCaptcha extends AntiCaptcha implements ProviderInterface
{
    protected $websiteURL;
    protected $websiteKey;
    protected $userAgent;
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
        string $userAgent,
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
        $this->userAgent  = $userAgent;

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
            'type'          => empty($this->proxyAddress) ? 'HCaptchaTaskProxyless' : 'HCaptchaTask',
            'websiteURL'    => $this->websiteURL,
            'websiteKey'    => $this->websiteKey,
            'userAgent'     => $this->userAgent,
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
    public function balance()
    {
        return $this->getBalance();
    }
}
