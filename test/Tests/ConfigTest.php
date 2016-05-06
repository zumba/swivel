<?php

namespace Tests;

use Zumba\Swivel\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBucket()
    {
        $index = 4;
        $config = new Config(['A' => [7, 8, 9]], 4);
        $bucket = $config->getBucket();
        $this->assertInstanceOf('Zumba\Swivel\Bucket', $bucket);
    }

    public function testSetBucketIndex()
    {
        $config = new Config();
        $image = new \ReflectionClass($config);
        $index = $image->getProperty('index');
        $index->setAccessible(true);
        $this->assertNull($index->getValue($config));

        $config->setBucketIndex(5);
        $this->assertEquals(5, $index->getValue($config));
    }

    public function testSetMetrics()
    {
        $config = new Config();
        $image = new \ReflectionClass($config);
        $metrics = $image->getProperty('metrics');
        $metrics->setAccessible(true);
        $this->assertNull($metrics->getValue($config));

        $metricsInstance = $this->getMock('Zumba\Swivel\MetricsInterface');

        $config->setMetrics($metricsInstance);
        $this->assertSame($metricsInstance, $metrics->getValue($config));
    }

    public function testAddMapInterface()
    {
        $map = $this->getMock('Zumba\Swivel\MapInterface');
        $map->expects($this->once())->method('setLogger');
        $config = new Config($map);
    }

    public function testAddDriverInterface()
    {
        $driver = $this->getMock('Zumba\Swivel\DriverInterface');
        $map = $this->getMock('Zumba\Swivel\MapInterface');

        $driver
            ->expects($this->once())
            ->method('getMap')
            ->will($this->returnValue($map));

        $map->expects($this->once())->method('setLogger');
        $config = new Config($driver);
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnknownObject()
    {
        $config = new Config(new \stdClass());
    }
}
