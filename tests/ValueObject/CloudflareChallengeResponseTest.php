<?php

namespace Crawly\CaptchaBreaker\Test\ValueObject;

use Crawly\CaptchaBreaker\ValueObject\CloudflareChallengeResponse;
use PHPUnit\Framework\TestCase;

class CloudflareChallengeResponseTest extends TestCase
{
    public function testGetCloudflareClearance(): void
    {
        $cloudflareChallengeResponse = new CloudflareChallengeResponse(
            "cloudflare-clearance",
            "cfuvid"
        );
        $this->assertEquals(
            "cloudflare-clearance",
            $cloudflareChallengeResponse->getCloudflareClearance()
        );

        $this->assertEquals(
            "cfuvid",
            $cloudflareChallengeResponse->getCfuvid()
        );
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function validationProvider(): array
    {
        return [
            ["", "", "Cloudflare clearance cannot be empty"],
            [" ", "", "Cloudflare clearance cannot be empty"],
            ["\t", "", "Cloudflare clearance cannot be empty"],
            ["\n", "", "Cloudflare clearance cannot be empty"],
            ["\r", "", "Cloudflare clearance cannot be empty"],
            ["\0", "", "Cloudflare clearance cannot be empty"],
            ["\x0B", "", "Cloudflare clearance cannot be empty"],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(
        string $cloudflareClearance,
        string $cfuvid,
        string $expectedMessage
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        new CloudflareChallengeResponse($cloudflareClearance, $cfuvid);
    }

    public function testNullableCfuvidCookie(): void
    {
        $cloudflareChallengeResponse = new CloudflareChallengeResponse(
            "cloudflare-clearance",
            null
        );
        $this->assertEquals(
            "cloudflare-clearance",
            $cloudflareChallengeResponse->getCloudflareClearance()
        );

        $this->assertEquals(
            null,
            $cloudflareChallengeResponse->getCfuvid()
        );
    }
}
