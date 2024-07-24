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
            "token"
        );
        $this->assertEquals(
            "cloudflare-clearance",
            $cloudflareChallengeResponse->getCloudflareClearance()
        );
    }

    public function testGetToken(): void
    {
        $cloudflareChallengeResponse = new CloudflareChallengeResponse(
            "cloudflare-clearance",
            "token"
        );
        $this->assertEquals("token", $cloudflareChallengeResponse->getToken());
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function validationProvider(): array
    {
        return [
            ["", "token", "Cloudflare clearance cannot be empty"],
            ["cloudflare-clearance", "", "Token cannot be empty"],
            ["", "", "Cloudflare clearance cannot be empty"],
            [" ", " ", "Cloudflare clearance cannot be empty"],
            ["\t", "\t", "Cloudflare clearance cannot be empty"],
            ["\n", "\n", "Cloudflare clearance cannot be empty"],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(
        string $cloudflareClearance,
        string $token,
        string $expectedMessage
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        new CloudflareChallengeResponse($cloudflareClearance, $token);
    }
}
