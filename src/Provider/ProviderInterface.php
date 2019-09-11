<?php


namespace Crawly\CaptchaBreaker\Provider;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;

interface ProviderInterface
{
    /**
     * @return string
     * @throws TaskCreationFailedException If the captcha breaker provider throws a task creation exception.
     * @throws BreakFailedException
     */
    public function solve(): string;

    /**
     * @return float
     * @throws BalanceFailedException
     */
    public function balance();
}
