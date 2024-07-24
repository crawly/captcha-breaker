<?php

namespace Crawly\CaptchaBreaker\ValueObject;

class CloudflareChallengeResponse implements ChallengeResponseContract
{
    private string $cloudflareClearance;
    private string $token;

    public function __construct(string $cloudflareClearance, string $token)
    {
        if (empty(trim($cloudflareClearance))) {
            throw new \InvalidArgumentException(
                "Cloudflare clearance cannot be empty"
            );
        }

        if (empty(trim($token))) {
            throw new \InvalidArgumentException("Token cannot be empty");
        }

        $this->cloudflareClearance = $cloudflareClearance;
        $this->token = $token;
    }

    public function getCloudflareClearance(): string
    {
        return $this->cloudflareClearance;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
