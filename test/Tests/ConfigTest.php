<?php
namespace Tests;

use \Zumba\Swivel\Config,
    \Zumba\Swivel\Bucket;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testGetBucket() {
        $index = 4;
        $config = new Config(['A' => [7,8,9]], 4);
        $bucket = $config->getBucket();
        $this->assertInstanceOf('Zumba\Swivel\Bucket', $bucket);
    }

    public function testSetBucket() {
        $config = new Config();
        $image = new \ReflectionClass($config);
        $index = $image->getProperty('index');
        $index->setAccessible(true);
        $this->assertNull($index->getValue($config));

        $config->setBucket(5);
        $this->assertEquals(5, $index->getValue($config));
    }
}
