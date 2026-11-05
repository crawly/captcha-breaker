<?php

namespace Crawly\CaptchaBreaker\Test\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\AntiCaptcha\Amazon;
use Crawly\CaptchaBreaker\Provider\CapMonster\Amazon as CapMonsterAmazon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AmazonTest extends TestCase
{
    private function makeProvider(): Amazon
    {
        return new Amazon('clientKey', 'websiteURL', 'challengeScript', 'captchaScript', 'websiteKey', 'context', 'iv');
    }

    public function testGetPostData()
    {
        $getPostData = (new ReflectionClass(Amazon::class))->getMethod('getPostData');
        $getPostData->setAccessible(true);

        $this->assertSame(
            [
                'type'            => 'AmazonTaskProxyless',
                'websiteURL'      => 'websiteURL',
                'challengeScript' => 'challengeScript',
                'captchaScript'   => 'captchaScript',
                'websiteKey'      => 'websiteKey',
                'context'         => 'context',
                'iv'              => 'iv',
            ],
            $getPostData->invoke($this->makeProvider())
        );
    }

    public function testConstructorParameterNamesMatchTheCapMonsterProvider()
    {
        $names = function (string $class): array {
            return array_map(
                function (\ReflectionParameter $parameter) {
                    return $parameter->getName();
                },
                (new ReflectionClass($class))->getConstructor()->getParameters()
            );
        };

        $this->assertSame(
            array_slice($names(CapMonsterAmazon::class), 0, 7),
            array_slice($names(Amazon::class), 0, 7)
        );
    }

    public function testTaskSolutionIsTheToken()
    {
        $provider = $this->makeProvider();
        $reflection = new ReflectionClass(Amazon::class);

        $taskInfo = $reflection->getParentClass()->getProperty('taskInfo');
        $taskInfo->setAccessible(true);
        $taskInfo->setValue($provider, json_decode('{"solution":{"token":"aws-waf-token-value","userAgent":"agent"}}'));

        $getTaskSolution = $reflection->getMethod('getTaskSolution');
        $getTaskSolution->setAccessible(true);

        $this->assertSame('aws-waf-token-value', $getTaskSolution->invoke($provider));
    }
}
