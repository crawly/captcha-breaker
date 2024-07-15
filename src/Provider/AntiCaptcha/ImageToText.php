<?php


namespace Crawly\CaptchaBreaker\Provider\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\ProviderInterface;
use Crawly\CaptchaBreaker\ValueObject\ChallengeResponse;
use Psr\Log\LoggerInterface;

/**
 * Class NoCaptcha
 * @package Crawly\CaptchaBreaker\Provider\AntiCaptcha
 *
 * Class that manages all NoCaptcha challenges.
 */
class ImageToText extends AntiCaptcha implements ProviderInterface
{
    protected $base64Image;
    protected $phrase;
    protected $case;
    protected $numeric;
    protected $math;
    protected $minLength;
    protected $maxLength;

    public function __construct(
        string $clientKey,
        string $base64Image,
        LoggerInterface $logger = null,
        bool $phrase = false,
        bool $case = false,
        int $numeric = 0,
        bool $math = false,
        int $minLength = 0,
        int $maxLength = 0
    ) {
        $this->clientKey   = $clientKey;
        $this->base64Image = $base64Image;

        $this->logger    = $logger;
        $this->phrase    = $phrase;
        $this->case      = $case;
        $this->numeric   = $numeric;
        $this->math      = $math;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;

        parent::__construct();
    }

    protected function getPostData(): array
    {
        return [
            'type'      => 'ImageToTextTask',
            'body'      => $this->base64Image,
            'phrase'    => $this->phrase,
            'case'      => $this->case,
            'numeric'   => $this->numeric,
            'math'      => $this->math,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
        ];
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getTaskSolution(): string
    {
        return $this->taskInfo->solution->text;
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
