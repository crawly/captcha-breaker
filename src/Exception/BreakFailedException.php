<?php


namespace Crawly\CaptchaBreaker\Exception;

use Throwable;

/**
 * Class BreakFailedException
 * @package Crawly\CaptchaBreaker\Exception
 */
class BreakFailedException extends \Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message ?? 'Could not break the provided captcha');
    }
}
