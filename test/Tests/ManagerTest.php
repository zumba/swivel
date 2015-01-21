<?php
namespace Tests;

use \Zumba\Swivel\Bucket;
use \Zumba\Swivel\Feature\Builder;
use \Zumba\Swivel\Manager;
use \Zumba\Swivel\Config;

class ManagerTest extends \PHPUnit_Framework_TestCase {
    public function testForFeature() {
        $manager = new Manager(new Config());
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);

        $manager->setBucket($bucket);
        $this->assertInstanceOf('Zumba\Swivel\Feature\Builder', $manager->forFeature('Test'));
    }

    public function testSetBucketReturnsManager() {
        $manager = new Manager(new Config());
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $this->assertInstanceOf('Zumba\Swivel\Manager', $manager->setBucket($bucket));
    }
}
