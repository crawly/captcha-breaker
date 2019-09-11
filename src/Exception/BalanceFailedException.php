<?php


namespace Crawly\CaptchaBreaker\Exception;

class BalanceFailedException extends \Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message ?? 'CaptchaBreaker was unable to communicate with the captcha provider.');
    }
}
