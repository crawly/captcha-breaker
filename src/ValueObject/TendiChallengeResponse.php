<?php

namespace Crawly\CaptchaBreaker\ValueObject;

class TendiChallengeResponse implements ChallengeResponseContract
{
    protected $ticket;

    protected $randomString;

    public function __construct(string $ticket, string $randomString)
    {
        $this->ticket = $ticket;
        $this->randomString = $randomString;
    }

    public function getTicket(): string
    {
        return $this->ticket;
    }

    public function getRandomString(): string
    {
        return $this->randomString;
    }
}
