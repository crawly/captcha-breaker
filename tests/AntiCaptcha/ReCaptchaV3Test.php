<?php


namespace Crawly\CaptchaBreaker\Test\AntiCaptcha;


use Crawly\CaptchaBreaker\Provider\AntiCaptcha\ReCaptchaV3;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ReCaptchaV3Test extends TestCase
{
    public function testGetPostData()
    {
        $noCaptcha = $this->getMockBuilder(ReCaptchaV3::class)->setConstructorArgs(['123', 'url', 'key', 'action', ReCaptchaV3::MIN_SCORE_0_3])->getMock();

        $stub = $this->getNoCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($noCaptcha);

        $this->assertEquals('RecaptchaV3TaskProxyless', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('action', $postData['pageAction']);
        $this->assertEquals(ReCaptchaV3::MIN_SCORE_0_3, $postData['minScore']);
    }

    protected function getNoCaptchaReflection(): ReflectionClass
    {
        return new ReflectionClass(ReCaptchaV3::class);
    }

    protected function getPostDataMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getPostData = $stub->getMethod('getPostData');
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}