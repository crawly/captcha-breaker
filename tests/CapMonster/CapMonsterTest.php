<?php

namespace Crawly\CaptchaBreaker\Test\CapMonster;

use Crawly\CaptchaBreaker\Exception\BalanceFailedException;
use Crawly\CaptchaBreaker\Exception\BreakFailedException;
use Crawly\CaptchaBreaker\Exception\TaskCreationFailedException;
use Crawly\CaptchaBreaker\Provider\CapMonster\CapMonster;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CapMonsterTest extends TestCase
{
    public function testCreateTaskSuccess()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockTaskCreate()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $createTask     = $this->createTaskMethod($stub);
        $getTaskId      = $this->getTaskIdMethod($stub);

        $instanceClient->invoke($capMonster);
        $createTask->invoke($capMonster);

        $taskId = $getTaskId->invoke($capMonster);

        $this->assertEquals("735497", $taskId);
    }

    public function testCreateTaskError()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockError()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $createTask     = $this->createTaskMethod($stub);

        $instanceClient->invoke($capMonster);

        $this->expectException(TaskCreationFailedException::class);

        $createTask->invoke($capMonster);
    }

    public function testGetBalanceSuccess()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockGetBalance()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $getBalance     = $this->getBalanceMethod($stub);

        $instanceClient->invoke($capMonster);
        $balance = $getBalance->invoke($capMonster);

        $this->assertEquals(12.3456, $balance);
    }

    public function testGetBalanceError()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockError()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $getBalance     = $this->getBalanceMethod($stub);

        $instanceClient->invoke($capMonster);

        $this->expectException(BalanceFailedException::class);

        $getBalance->invoke($capMonster);
    }

    public function testWaitForResultSuccess()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockTaskResult()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $waitForResult  = $this->waitForResultMethod($stub);
        $getTaskInfo    = $this->getTaskInfoMethod($stub);

        $instanceClient->invoke($capMonster);
        $waitForResult->invoke($capMonster);

        $taskInfo = $getTaskInfo->invoke($capMonster);

        $this->assertEquals("deditur", $taskInfo->solution->text);
    }

    public function testWaitForResultProcessing()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockTaskResultProcessing()));
        $mockHandler->append(new Response(200, [], $this->mockTaskResult()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $waitForResult  = $this->waitForResultMethod($stub);
        $getTaskInfo    = $this->getTaskInfoMethod($stub);

        $instanceClient->invoke($capMonster);
        $waitForResult->invoke($capMonster);

        $taskInfo = $getTaskInfo->invoke($capMonster);

        $this->assertEquals("deditur", $taskInfo->solution->text);
    }

    public function testWaitForResultProcessingRetry()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockTaskResultProcessing()));
        $mockHandler->append(new ConnectException("", new Request('GET', 'test')));
        $mockHandler->append(new Response(200, [], $this->mockTaskResult()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $waitForResult  = $this->waitForResultMethod($stub);
        $getTaskInfo    = $this->getTaskInfoMethod($stub);

        $instanceClient->invoke($capMonster);
        $waitForResult->invoke($capMonster);

        $taskInfo = $getTaskInfo->invoke($capMonster);

        $this->assertEquals("deditur", $taskInfo->solution->text);
    }

    public function testWaitForResultError()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockError()));

        $capMonster = $this->mock($mockHandler);

        $stub           = $this->getCapMonsterReflection();
        $instanceClient = $this->instanceClientMethod($stub);
        $waitForResult  = $this->waitForResultMethod($stub);

        $instanceClient->invoke($capMonster);

        $this->expectException(BreakFailedException::class);

        $waitForResult->invoke($capMonster);
    }

    public function testReportIncorrectImageSuccess()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockReportSuccess()));

        $capMonster = $this->mock($mockHandler);

        $stub            = $this->getCapMonsterReflection();
        $instanceClient  = $this->instanceClientMethod($stub);
        $reportIncorrect = $this->reportIncorrectMethod($stub);

        $instanceClient->invoke($capMonster);
        $report = $reportIncorrect->invokeArgs($capMonster, [735497, true]);

        $this->assertTrue($report);
    }

    public function testReportIncorrectRecaptchaSuccess()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockReportSuccess()));

        $capMonster = $this->mock($mockHandler);

        $stub            = $this->getCapMonsterReflection();
        $instanceClient  = $this->instanceClientMethod($stub);
        $reportIncorrect = $this->reportIncorrectMethod($stub);

        $instanceClient->invoke($capMonster);
        $report = $reportIncorrect->invokeArgs($capMonster, [735497, false]);

        $this->assertTrue($report);
    }

    public function testReportIncorrectError()
    {
        $mockHandler = new MockHandler();
        $mockHandler->append(new Response(200, [], $this->mockReportError()));

        $capMonster = $this->mock($mockHandler);

        $stub            = $this->getCapMonsterReflection();
        $instanceClient  = $this->instanceClientMethod($stub);
        $reportIncorrect = $this->reportIncorrectMethod($stub);

        $instanceClient->invoke($capMonster);
        $report = $reportIncorrect->invokeArgs($capMonster, [735497, true]);

        $this->assertFalse($report);
    }

    protected function mock(MockHandler $mockHandler)
    {
        $mock = $this->getMockBuilder(CapMonster::class)->onlyMethods([
            'getClientHandler',
            'sleep',
            'getRetryDelaySeconds',
        ])->disableOriginalConstructor()->getMockForAbstractClass();
        $mock->method('sleep');
        $mock->method('getClientHandler')
            ->willReturn($mockHandler);
        $mock->method('getPostData')
            ->willReturn([
                "type"      => 'ImageToTextTask',
                "body"      => base64_encode(''),
                "phrase"    => false,
                "case"      => false,
                "numeric"   => 0,
                "math"      => false,
                "minLength" => 0,
                "maxLength" => 0,
            ]);
        $mock->method('getRetryDelaySeconds')->willReturn(0);

        return $mock;
    }

    protected function mockTaskCreate(): string
    {
        return '
            {
                "errorId": 0,
                "taskId": 735497
            }
        ';
    }

    protected function mockError(): string
    {
        return '
            {
                "errorId": 1,
                "errorCode": "ERROR_KEY_DOES_NOT_EXIST",
                "errorDescription": "Account authorization key not found in the system"
            }
        ';
    }

    protected function mockTaskResult(): string
    {
        return '
            {
                "errorId": 0,
                "status": "ready",
                "solution": {
                    "text": "deditur",
                    "url": "http:\/\/61.39.233.233\/1\/147220556452507.jpg"
                },
                "cost": "0.000700",
                "ip": "46.98.54.221",
                "createTime": 1472205564,
                "endTime": 1472205570,
                "solveCount": 0
            }
        ';
    }

    protected function mockTaskResultProcessing(): string
    {
        return '
            {
                "errorId": 0,
                "status": "processing"
            }
        ';
    }

    protected function mockGetBalance()
    {
        return '
            {
                "errorId":0,
                "balance":12.3456
            }
        ';
    }

    protected function mockReportSuccess()
    {
        return '
            {
                "errorId":0,
                "status":"success"
            }
        ';
    }

    protected function mockReportError()
    {
        return '
            {
                "errorId":16,
                "status":"success"
            }
        ';
    }

    protected function getCapMonsterReflection(): ReflectionClass
    {
        return new ReflectionClass(CapMonster::class);
    }

    protected function createTaskMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $createTask = $stub->getMethod('createTask');
        $createTask->setAccessible(true);

        return $createTask;
    }

    protected function instanceClientMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $instanceClient = $stub->getMethod('instanceClient');
        $instanceClient->setAccessible(true);

        return $instanceClient;
    }

    protected function getTaskIdMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getTaskId = $stub->getMethod('getTaskId');
        $getTaskId->setAccessible(true);

        return $getTaskId;
    }

    protected function getBalanceMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getBalance = $stub->getMethod('getBalance');
        $getBalance->setAccessible(true);

        return $getBalance;
    }

    protected function waitForResultMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $waitForResult = $stub->getMethod('waitForResult');
        $waitForResult->setAccessible(true);

        return $waitForResult;
    }

    protected function getTaskInfoMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $getTaskInfo = $stub->getMethod('getTaskInfo');
        $getTaskInfo->setAccessible(true);

        return $getTaskInfo;
    }

    protected function reportIncorrectMethod(ReflectionClass $stub): \ReflectionMethod
    {
        $reportIncorrect = $stub->getMethod('reportIncorrect');
        $reportIncorrect->setAccessible(true);

        return $reportIncorrect;
    }
}