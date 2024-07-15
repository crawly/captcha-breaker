<?php

namespace Crawly\CaptchaBreaker\ValueObject;

class ChallengeResponse implements ChallengeResponseContract
{
    protected $result;

    public function __construct(string $result)
    {
        $this->result = $result;
    }

    public function getResult(): string
    {
        return $this->result;
    }
}
