<?php


namespace Crawly\CaptchaBreaker\Test\AntiCaptcha;


use Crawly\CaptchaBreaker\Provider\AntiCaptcha\NoCaptcha;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NoCaptchaTest extends TestCase
{
    public function testGetPostData()
    {
        $noCaptcha = $this->getMockBuilder(NoCaptcha::class)->setConstructorArgs(['123', 'url', 'key'])->getMock();

        $stub = $this->getNoCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($noCaptcha);

        $this->assertEquals('NoCaptchaTaskProxyless', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('', $postData['websiteSToken']);
        $this->assertEquals('http', $postData['proxyType']);
        $this->assertEquals('', $postData['proxyAddress']);
        $this->assertEquals('', $postData['proxyPort']);
        $this->assertEquals('', $postData['proxyLogin']);
        $this->assertEquals('', $postData['proxyPassword']);
        $this->assertEquals('', $postData['cookies']);
        $this->assertEquals('', $postData['websiteSToken']);
        $this->assertEquals(false, $postData['isInvisible']);
    }

    public function testGetPostDataWithAllParams()
    {
        $noCaptcha = $this->getMockBuilder(NoCaptcha::class)->setConstructorArgs([
            '123',
            'url',
            'key',
            null,
            'proxy-address',
            'proxy-port',
            'proxy-login',
            'proxy-password',
            'proxy-type',
            'cookies',
            'website-stoken',
            true,
        ])->getMock();

        $stub = $this->getNoCaptchaReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($noCaptcha);

        $this->assertEquals('NoCaptchaTask', $postData['type']);
        $this->assertEquals('url', $postData['websiteURL']);
        $this->assertEquals('key', $postData['websiteKey']);
        $this->assertEquals('proxy-type', $postData['proxyType']);
        $this->assertEquals('proxy-address', $postData['proxyAddress']);
        $this->assertEquals('proxy-port', $postData['proxyPort']);
        $this->assertEquals('proxy-login', $postData['proxyLogin']);
        $this->assertEquals('proxy-password', $postData['proxyPassword']);
        $this->assertEquals('cookies', $postData['cookies']);
        $this->assertEquals('website-stoken', $postData['websiteSToken']);
        $this->assertEquals(true, $postData['isInvisible']);
    }

    protected function getNoCaptchaReflection(): ReflectionClass
    {
        return new ReflectionClass(NoCaptcha::class);
    }

    protected function getPostDataMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getPostData = $stub->getMethod('getPostData');
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}