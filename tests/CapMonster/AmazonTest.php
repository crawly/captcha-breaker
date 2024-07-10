<?php

namespace Crawly\CaptchaBreaker\Test\CapMonster;

use Crawly\CaptchaBreaker\Provider\CapMonster\Amazon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AmazonTest extends TestCase
{
    public function testGetPostData() {
        // given
        $mock = $this->getMockBuilder(Amazon::class)
            ->setConstructorArgs([
                'websiteURL phpunit',
                'challengeScript crawly',
                'captchaScript phpunit',
                'websiteKey crawly',
                'context phpunit',
                'iv crawly',
                true
            ])
            ->getMock();
        $stub = $this->getNoCaptchaReflection();
        $getPostDataMethod = $this->getPostDataMethod($stub);

        // when
        $postData = $getPostDataMethod->invoke($mock);

        // then
        $this->assertEquals('AmazonTaskProxyless', $postData['type']);
        $this->assertEquals('websiteURL phpunit', $postData['websiteURL']);
        $this->assertEquals('challengeScript crawly', $postData['challengeScript']);
        $this->assertEquals('captchaScript phpunit', $postData['captchaScript']);
        $this->assertEquals('websiteKey crawly', $postData['websiteKey']);
        $this->assertEquals('context phpunit', $postData['context']);
        $this->assertEquals('iv crawly', $postData['iv']);
        $this->assertTrue($postData['cookieSolution']);
    }
    private function getNoCaptchaReflection(): ReflectionClass
    {
        return new ReflectionClass(Amazon::class);
    }
    private function getPostDataMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getPostData = $stub->getMethod('getPostData');
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}