<?php

namespace Crawly\CaptchaBreaker\Test\CapMonster;

use Crawly\CaptchaBreaker\Provider\CapMonster\CloudflareChallengeWithCookiesCaptcha;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CloudflareChallengeWithCookiesCaptchaTest extends TestCase
{
    public function testGetPostData(): void
    {
        $cloudflareChallengeWithCookiesCaptcha = $this->getMockBuilder(
            CloudflareChallengeWithCookiesCaptcha::class
        )
            ->setConstructorArgs([
                "123",
                "url",
                "key",
                "html",
                "user-agent",
                "proxy-address",
                "proxy-port",
                "proxy-login",
                "proxy-password",
                "proxy-type",
            ])
            ->getMock();

        $stub = $this->getCloudflareChallengeWithCookiesCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke(
            $cloudflareChallengeWithCookiesCaptcha
        );

        $this->assertEquals("TurnstileTask", $postData["type"]);
        $this->assertEquals("url", $postData["websiteURL"]);
        $this->assertEquals("key", $postData["websiteKey"]);
        $this->assertEquals("cf_clearance", $postData["cloudflareTaskType"]);
        $this->assertEquals("html", $postData["htmlPageBase64"]);
    }

    public function testGetPostDataWithAllParams()
    {
        $cloudflareChallengeWithCookiesCaptcha = $this->getMockBuilder(
            CloudflareChallengeWithCookiesCaptcha::class
        )
            ->setConstructorArgs([
                "123",
                "url",
                "key",
                "html",
                "user-agent",
                "proxy-address",
                "proxy-port",
                "proxy-login",
                "proxy-password",
                "proxy-type",
            ])
            ->getMock();

        $stub = $this->getCloudflareChallengeWithCookiesCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke(
            $cloudflareChallengeWithCookiesCaptcha
        );

        $this->assertEquals("TurnstileTask", $postData["type"]);
        $this->assertEquals("url", $postData["websiteURL"]);
        $this->assertEquals("key", $postData["websiteKey"]);
        $this->assertEquals("cf_clearance", $postData["cloudflareTaskType"]);
        $this->assertEquals("html", $postData["htmlPageBase64"]);
    }

    private function getCloudflareChallengeWithCookiesCaptchaReflection()
    {
        $cloudflareChallengeWithCookiesCaptcha = new ReflectionClass(
            CloudflareChallengeWithCookiesCaptcha::class
        );

        return $cloudflareChallengeWithCookiesCaptcha;
    }

    private function getPostDataMethod($stub)
    {
        $getPostData = $stub->getMethod("getPostData");
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}
