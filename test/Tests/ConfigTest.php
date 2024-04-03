<?php

namespace Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use Zumba\Swivel\Bucket;
use Zumba\Swivel\Config;
use Zumba\Swivel\DriverInterface;
use Zumba\Swivel\MapInterface;
use Zumba\Swivel\MetricsInterface;

class ConfigTest extends TestCase
{
    public function testGetBucket()
    {
        $config = new Config(['A' => [7, 8, 9]], 4);
        $bucket = $config->getBucket();
        $this->assertInstanceOf(Bucket::class, $bucket);
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

        $metricsInstance = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();

        $config->setMetrics($metricsInstance);
        $this->assertSame($metricsInstance, $metrics->getValue($config));
    }

    public function testAddMapInterface()
    {
        $map = $this->getMockBuilder(MapInterface::class)
            ->getMock();
        $map->expects($this->once())->method('setLogger');
        $config = new Config($map);
    }

    public function testAddDriverInterface()
    {
        $driver = $this->getMockBuilder(DriverInterface::class)
            ->getMock();
        $map = $this->getMockBuilder(MapInterface::class)
            ->getMock();

        $driver
            ->expects($this->once())
            ->method('getMap')
            ->will($this->returnValue($map));

        $map->expects($this->once())->method('setLogger');
        $config = new Config($driver);
    }

    public function testUnknownObject()
    {
        $this->expectException(LogicException::class);

        $config = new Config(new \stdClass());
    }
}
