<?php


namespace Crawly\CaptchaBreaker\Test\AntiCaptcha;


use Crawly\CaptchaBreaker\Provider\AntiCaptcha\ImageToText;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ImageToTextTest extends TestCase
{
    public function testGetPostData()
    {
        $imageToText = $this->getMockBuilder(ImageToText::class)->setConstructorArgs(['123', base64_encode('')])->getMock();

        $stub = $this->getImageToTextReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($imageToText);

        $this->assertEquals('ImageToTextTask', $postData['type']);
        $this->assertEquals(base64_encode(''), $postData['body']);
        $this->assertEquals(false, $postData['phrase']);
        $this->assertEquals(false, $postData['case']);
        $this->assertEquals(0, $postData['numeric']);
        $this->assertEquals(false, $postData['math']);
        $this->assertEquals(0, $postData['minLength']);
        $this->assertEquals(0, $postData['maxLength']);
    }

    public function testGetPostDataWithAllParams()
    {
        $imageToText = $this->getMockBuilder(ImageToText::class)->setConstructorArgs(['123', base64_encode(''), null, true, true, 12, true, 7, 29])->getMock();

        $stub = $this->getImageToTextReflection();

        $getPostData = $this->getPostDataMethod($stub);

        $postData = $getPostData->invoke($imageToText);

        $this->assertEquals('ImageToTextTask', $postData['type']);
        $this->assertEquals(base64_encode(''), $postData['body']);
        $this->assertEquals(true, $postData['phrase']);
        $this->assertEquals(true, $postData['case']);
        $this->assertEquals(12, $postData['numeric']);
        $this->assertEquals(true, $postData['math']);
        $this->assertEquals(7, $postData['minLength']);
        $this->assertEquals(29, $postData['maxLength']);
    }

    protected function getImageToTextReflection(): ReflectionClass
    {
        return new ReflectionClass(ImageToText::class);
    }

    protected function getPostDataMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getPostData = $stub->getMethod('getPostData');
        $getPostData->setAccessible(true);

        return $getPostData;
    }
}