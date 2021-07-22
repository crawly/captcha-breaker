<?php


namespace Crawly\CaptchaBreaker\Test\AntiCaptcha;


use Crawly\CaptchaBreaker\Provider\AntiCaptcha\HCaptcha;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class HCaptchaTest extends TestCase
{
    public function testGetPostData()
    {
        $noCaptcha = $this->getMockBuilder(HCaptcha::class)->setConstructorArgs(['123', 'url', 'key', 'user-agent'])->getMock();

        $stub = $this->getNoCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($noCaptcha);

        $this->assertEquals('HCaptchaTaskProxyless', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('user-agent', $postData['userAgent']);
        $this->assertEquals('http', $postData['proxyType']);
        $this->assertEquals('', $postData['proxyAddress']);
        $this->assertEquals('', $postData['proxyPort']);
        $this->assertEquals('', $postData['proxyLogin']);
        $this->assertEquals('', $postData['proxyPassword']);
    }

    public function testGetPostDataWithAllParams()
    {
        $noCaptcha = $this->getMockBuilder(HCaptcha::class)->setConstructorArgs([
            '123',
            'url',
            'key',
            'user-agent',
            null,
            'proxy-address',
            'proxy-port',
            'proxy-login',
            'proxy-password',
            'proxy-type',
        ])->getMock();

        $stub = $this->getNoCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($noCaptcha);

        $this->assertEquals('HCaptchaTask', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('user-agent', $postData['userAgent']);
        $this->assertEquals('proxy-type', $postData['proxyType']);
        $this->assertEquals('proxy-address', $postData['proxyAddress']);
        $this->assertEquals('proxy-port', $postData['proxyPort']);
        $this->assertEquals('proxy-login', $postData['proxyLogin']);
        $this->assertEquals('proxy-password', $postData['proxyPassword']);
    }

    protected function getNoCaptchaReflection(): ReflectionClass
    {
        return new ReflectionClass(HCaptcha::class);
    }

    protected function getPostDataMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getPostData = $stub->getMethod('getPostData');
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}