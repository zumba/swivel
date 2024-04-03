<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Zumba\Swivel\Behavior;
use Zumba\Swivel\Bucket;
use Zumba\Swivel\Map;

class BucketTest extends TestCase
{
    public function testEnabledDelegatesToMap()
    {
        $map = $this->getMockBuilder(Map::class)
            ->onlyMethods(['enabled'])
            ->getMock();
        $behavior = $this->getMockBuilder(Behavior::class)
            ->onlyMethods(['getSlug'])
            ->setConstructorArgs(['test', fn() => null])
            ->getMock();
        $bucket = new Bucket($map, Bucket::FIFTH);

        $map->expects($this->once())
            ->method('enabled')
            ->with('Test.test', Bucket::FIFTH)
            ->will($this->returnValue(true));

        $behavior
            ->expects($this->once())
            ->method('getSlug')
            ->will($this->returnValue('Test.test'));

        $this->assertTrue($bucket->enabled($behavior));
    }

    /**
     * @dataProvider slugProvider
     */
    public function testCallbackReceivedSlug($slug, $mapArray)
    {
        $map = new \Zumba\Swivel\Map($mapArray);
        $behavior = new \Zumba\Swivel\Behavior($slug, fn() => null);

        $bucket = new Bucket($map, Bucket::FIRST, null, fn($slug_param) => $this->assertEquals($slug, $slug_param));

        $bucket->enabled($behavior);
    }

    public function slugProvider()
    {
        return [
            [
                'InvalidSlug',[
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1],
                ],
            ]
        ];
    }
}
