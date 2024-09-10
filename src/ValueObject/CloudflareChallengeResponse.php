<?php

namespace Crawly\CaptchaBreaker\ValueObject;

class CloudflareChallengeResponse implements ChallengeResponseContract
{
    private string $cloudflareClearance;

    public function __construct(string $cloudflareClearance, string $cfuvid)
    {
        if (empty(trim($cloudflareClearance))) {
            throw new \InvalidArgumentException(
                "Cloudflare clearance cannot be empty"
            );
        }

        if (empty(trim($cfuvid))) {
            throw new \InvalidArgumentException(
                "cfuvid cannot be empty"
            );
        }

        $this->cloudflareClearance = $cloudflareClearance;
        $this->cfuvid = $cfuvid;
    }

    public function getCloudflareClearance(): string
    {
        return $this->cloudflareClearance;
    }

    public function getCfuvid(): string
    {
        return $this->cfuvid;
    }
}
