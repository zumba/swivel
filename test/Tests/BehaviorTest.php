<?php
namespace Tests;

use \Zumba\Swivel\Behavior;

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
}
