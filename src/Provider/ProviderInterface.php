<?php

namespace Crawly\CaptchaBreaker\Provider;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;
use Crawly\CaptchaBreaker\ValueObject\ChallengeResponseContract;

interface ProviderInterface
{
    /**
     * @deprecated You should use resolveChallenge instead.
     * @return string
     * @throws TaskCreationFailedException If the captcha breaker provider throws a task creation exception.
     * @throws BreakFailedException
     */
    public function solve(): string;

    /**
     * @return ChallengeResponseContract
     * @throws TaskCreationFailedException If the captcha breaker provider throws a task creation exception.
     * @throws BreakFailedException
     */
    public function resolveChallenge(): ChallengeResponseContract;

    /**
     * @return float
     * @throws BalanceFailedException
     */
    public function balance();
}
