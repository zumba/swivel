<?php
namespace Tests;

use \Zumba\Swivel\Bucket;
use \Zumba\Swivel\Builder;
use \Zumba\Swivel\Manager;
use \Zumba\Swivel\Config;

class ManagerTest extends \PHPUnit_Framework_TestCase {
    public function testForFeature() {
        $manager = new Manager($this->getMock('Zumba\Swivel\Config'));
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);

        $manager->setBucket($bucket);
        $this->assertInstanceOf('Zumba\Swivel\Builder', $manager->forFeature('Test'));
    }

    public function testSetBucketReturnsManager() {
        $manager = new Manager($this->getMock('Zumba\Swivel\Config'));
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $this->assertInstanceOf('Zumba\Swivel\Manager', $manager->setBucket($bucket));
    }
}
