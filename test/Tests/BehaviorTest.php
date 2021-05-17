<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use stdClass;
use Zumba\Swivel\Behavior;

class BehaviorTest extends TestCase
{
    public function testExecute()
    {
        $mock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['callback'])
            ->getMock();
        $mock->expects($this->once())
            ->method('callback')
            ->with('a', 'b')
            ->will($this->returnValue(true));

        $behavior = new Behavior('a', [$mock, 'callback']);
        $this->assertTrue($behavior->execute(['a', 'b']));
    }

    public function testExecuteWithLogger()
    {
        $logger = $this->getMockBuilder(NullLogger::class)
            ->getMock();
        $behavior = new Behavior('a', function () {
        });
        $behavior->setLogger($logger);

        $logger->expects($this->once())
            ->method('debug')
            ->with($this->isType('string'), $this->logicalAnd(
                $this->arrayHasKey('slug'),
                $this->arrayHasKey('args')
            ));

        $behavior->execute();
    }
}
