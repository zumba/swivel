<?php
namespace Tests;

use \Zumba\Swivel\Bucket;
use \Zumba\Swivel\Map;
use \Zumba\Swivel\Behavior;

class BucketTest extends \PHPUnit_Framework_TestCase {
    public function testEnabledDelegatesToMap() {
        $map = $this->getMock('Zumba\Swivel\Map');
        $behavior = $this->getMock('Zumba\Swivel\Behavior', ['getSlug'], ['test', function() {}]);
        $bucket = new Bucket($map, Bucket::FIFTH);

        $map->expects($this->once())
            ->method('enabled')
            ->with('Test.test', Bucket::FIFTH)
            ->will($this->returnValue('test_result'));

        $behavior
            ->expects($this->once())
            ->method('getSlug')
            ->will($this->returnValue('Test.test'));

        $this->assertSame('test_result', $bucket->enabled($behavior));
    }

    /**
     * @dataProvider slugProvider
     */
    public function testCallbackReceivedSlug($slug, $mapArray)
    {
        $map = new \Zumba\Swivel\Map($mapArray);
        $behavior = new \Zumba\Swivel\Behavior($slug, function () {
        });

        $bucket = new Bucket($map, Bucket::FIRST, null, function ($slug_param) use ($slug) {
            $this->assertEquals($slug, $slug_param);
        });

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
