<?php
namespace Tests;

use \Zumba\Swivel\Bucket;
use \Zumba\Swivel\Builder;
use \Zumba\Swivel\Manager;
use \Zumba\Swivel\Config;

class ManagerTest extends \PHPUnit_Framework_TestCase {
    public function testForFeature() {
        $manager = new Manager(new Config());
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);

        $manager->setBucket($bucket);
        $this->assertInstanceOf('Zumba\Swivel\Builder', $manager->forFeature('Test'));
    }

    public function testSetBucketReturnsManager() {
        $manager = new Manager(new Config());
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $this->assertInstanceOf('Zumba\Swivel\Manager', $manager->setBucket($bucket));
    }

    public function testInvokeOneParamEnabled() {
        $config = $this->getMock('Zumba\Swivel\Config');
        $manager = $this->getMock('Zumba\Swivel\Manager', ['forFeature'], [$config]);
        $builder = $this
            ->getMockBuilder('Zumba\Swivel\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $manager
            ->expects($this->once())
            ->method('forFeature')
            ->with('Test')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('addBehavior')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with(null)
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertEquals('abc', $manager->invoke('Test.version.a', 'abc'));
    }

    public function testInvokeOneParamDisabled() {
        $config = $this->getMock('Zumba\Swivel\Config');
        $manager = $this->getMock('Zumba\Swivel\Manager', ['forFeature'], [$config]);
        $builder = $this
            ->getMockBuilder('Zumba\Swivel\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $manager
            ->expects($this->once())
            ->method('forFeature')
            ->with('Test')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('addBehavior')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with(null)
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute');

        $this->assertEquals(null, $manager->invoke('Test.version.a', 'abc'));
    }

    public function testInvokeTwoParamEnabled() {
        $config = $this->getMock('Zumba\Swivel\Config');
        $manager = $this->getMock('Zumba\Swivel\Manager', ['forFeature'], [$config]);
        $builder = $this
            ->getMockBuilder('Zumba\Swivel\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $manager
            ->expects($this->once())
            ->method('forFeature')
            ->with('Test')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('addBehavior')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with('default')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertEquals('abc', $manager->invoke('Test.version.a', 'abc', 'default'));
    }

    public function testInvokeTwoParamDisabled() {
        $config = $this->getMock('Zumba\Swivel\Config');
        $manager = $this->getMock('Zumba\Swivel\Manager', ['forFeature'], [$config]);
        $builder = $this
            ->getMockBuilder('Zumba\Swivel\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $manager
            ->expects($this->once())
            ->method('forFeature')
            ->with('Test')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('addBehavior')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with('default')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('default'));

        $this->assertEquals('default', $manager->invoke('Test.version.a', 'abc', 'default'));
    }
}
