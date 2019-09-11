<?php

namespace Crawly\CaptchaBreaker\Exception;

/**
 * Class TaskCreationFailedException
 * @package Crawly\CaptchaBreaker\Exception
 */
class TaskCreationFailedException extends \Exception
{
    /**
     * TaskCreationFailedException constructor.
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'CaptchaBreaker was unable to communicate with the captcha provider.');
    }
}
