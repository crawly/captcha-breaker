<?php

namespace Crawly\CaptchaBreaker\ValueObject;

class CloudflareChallengeResponse implements ChallengeResponseContract
{
    private string $cloudflareClearance;

    public function __construct(string $cloudflareClearance)
    {
        if (empty(trim($cloudflareClearance))) {
            throw new \InvalidArgumentException(
                "Cloudflare clearance cannot be empty"
            );
        }

        $this->cloudflareClearance = $cloudflareClearance;
    }

    public function getCloudflareClearance(): string
    {
        return $this->cloudflareClearance;
    }
}
