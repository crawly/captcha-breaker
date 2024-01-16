<?php


namespace Crawly\CaptchaBreaker\Test\CapMonster;


use Crawly\CaptchaBreaker\Provider\CapMonster\TurnstileCaptcha;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TurnstileCaptchaTest extends TestCase
{
    public function testGetPostData()
    {
        $turnstileCaptcha = $this->getMockBuilder(TurnstileCaptcha::class)->setConstructorArgs(['123', 'url', 'key'])->getMock();

        $stub = $this->getTurnstileCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($turnstileCaptcha);

        $this->assertEquals('TurnstileTaskProxyless', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('http', $postData['proxyType']);
        $this->assertEquals('', $postData['proxyAddress']);
        $this->assertEquals('', $postData['proxyPort']);
        $this->assertEquals('', $postData['proxyLogin']);
        $this->assertEquals('', $postData['proxyPassword']);
    }

    public function testGetPostDataWithAllParams()
    {
        $turnstileCaptcha = $this->getMockBuilder(TurnstileCaptcha::class)->setConstructorArgs([
            '123',
            'url',
            'key',
            null,
            'proxy-address',
            'proxy-port',
            'proxy-login',
            'proxy-password',
            'proxy-type',
        ])->getMock();

        $stub = $this->getTurnstileCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($turnstileCaptcha);

        $this->assertEquals('TurnstileTask', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('proxy-type', $postData['proxyType']);
        $this->assertEquals('proxy-address', $postData['proxyAddress']);
        $this->assertEquals('proxy-port', $postData['proxyPort']);
        $this->assertEquals('proxy-login', $postData['proxyLogin']);
        $this->assertEquals('proxy-password', $postData['proxyPassword']);
    }

    protected function getTurnstileCaptchaReflection(): ReflectionClass
    {
        return new ReflectionClass(TurnstileCaptcha::class);
    }

    protected function getPostDataMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getPostData = $stub->getMethod('getPostData');
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}
