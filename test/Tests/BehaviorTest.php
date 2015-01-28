<?php
namespace Tests;

use \Zumba\Swivel\Behavior;
use \Psr\Log\NullLogger;

class BehaviorTest extends \PHPUnit_Framework_TestCase {
    public function testExecute() {
        $mock = $this->getMock('stdClass', ['callback']);
        $mock->expects($this->once())
            ->method('callback')
            ->with('a', 'b')
            ->will($this->returnValue(true));

        $behavior = new Behavior('a', [$mock, 'callback']);
        $this->assertTrue($behavior->execute(['a', 'b']));
    }

    public function testExecuteWithLogger() {
        $logger = $this->getMock('Psr\Log\NullLogger');
        $behavior = new Behavior('a', function() {});
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
