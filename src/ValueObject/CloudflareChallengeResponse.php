<?php

namespace Crawly\CaptchaBreaker\ValueObject;

class CloudflareChallengeResponse implements ChallengeResponseContract
{
    private $cloudflareClearance;
    private $cfuvid;

    public function __construct(string $cloudflareClearance, ?string $cfuvid)
    {
        if (empty(trim($cloudflareClearance))) {
            throw new \InvalidArgumentException(
                "Cloudflare clearance cannot be empty"
            );
        }

        $this->cloudflareClearance = $cloudflareClearance;
        $this->cfuvid = $cfuvid;
    }

    public function getCloudflareClearance(): string
    {
        return $this->cloudflareClearance;
    }

    public function getCfuvid(): ?string
    {
        return $this->cfuvid;
    }
}
