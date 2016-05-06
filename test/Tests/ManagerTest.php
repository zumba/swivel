<?php

namespace Tests;

use Zumba\Swivel\Manager;
use Zumba\Swivel\Config;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testForFeature()
    {
        $manager = new Manager(new Config());
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);

        $manager->setBucket($bucket);
        $this->assertInstanceOf('Zumba\Swivel\Builder', $manager->forFeature('Test'));
    }

    public function testSetBucketReturnsManager()
    {
        $manager = new Manager(new Config());
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $this->assertInstanceOf('Zumba\Swivel\Manager', $manager->setBucket($bucket));
    }

    public function testInvokeOneParamEnabled()
    {
        $config = new Config();
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
            ->with('version.a', $this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with($this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertEquals('abc', $manager->invoke('Test.version.a', function () { return 'abc'; }));
    }

    public function testInvokeOneParamDisabled()
    {
        $config = new Config();
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
            ->with('version.a', $this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with($this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute');

        $this->assertEquals(null, $manager->invoke('Test.version.a', function () { return 'abc'; }));
    }

    public function testInvokeTwoParamEnabled()
    {
        $config = new Config();
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
            ->with('version.a', $this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with($this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertEquals('abc', $manager->invoke(
            'Test.version.a',
            function () { return 'abc'; },
            function () { return 'default'; }
        ));
    }

    public function testInvokeTwoParamDisabled()
    {
        $config = new Config();
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
            ->with('version.a', $this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultBehavior')
            ->with($this->isType('callable'))
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('default'));

        $this->assertEquals('default', $manager->invoke(
            'Test.version.a',
            function () { return 'abc'; },
            function () { return 'default'; }
        ));
    }

    public function testSetMetrics()
    {
        $config = $this->getMock('Zumba\Swivel\Config', ['getMetrics']);
        $metricsInstance = $this->getMock('Zumba\Swivel\MetricsInterface');

        $config
            ->expects($this->once())
            ->method('getMetrics')
            ->will($this->returnValue($metricsInstance));

        $manager = new Manager($config);
        $image = new \ReflectionClass($manager);
        $metrics = $image->getProperty('metrics');
        $metrics->setAccessible(true);
        $this->assertSame($metricsInstance, $metrics->getValue($manager));
    }

    public function testReturnValueOneParamEnabled()
    {
        $config = new Config();
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
            ->method('addValue')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultValue')
            ->with(null)
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertEquals('abc', $manager->returnValue('Test.version.a', 'abc'));
    }

    public function testReturnValueOneParamDisabled()
    {
        $config = new Config();
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
            ->method('addValue')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultValue')
            ->with(null)
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute');

        $this->assertEquals(null, $manager->returnValue('Test.version.a', 'abc'));
    }

    public function testReturnValueTwoParamEnabled()
    {
        $config = new Config();
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
            ->method('addValue')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultValue')
            ->with('default')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertEquals('abc', $manager->returnValue('Test.version.a', 'abc', 'default'));
    }

    public function testReturnValueTwoParamDisabled()
    {
        $config = new Config();
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
            ->method('addValue')
            ->with('version.a', 'abc')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('defaultValue')
            ->with('default')
            ->will($this->returnValue($builder));

        $builder
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('default'));

        $this->assertEquals('default', $manager->returnValue('Test.version.a', 'abc', 'default'));
    }
}
