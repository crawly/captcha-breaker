<?php

namespace Crawly\CaptchaBreaker\Test\AntiCaptcha;

use Crawly\CaptchaBreaker\Provider\AntiCaptcha\ImageToText;
use PHPUnit\Framework\TestCase;

class ImageToTextTest extends TestCase
{
    protected function mock()
    {
        $mock = $this->createMock(ImageToText::class);
        $mock->method('sleep');
    }
}