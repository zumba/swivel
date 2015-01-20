<?php
namespace Tests;

use \Zumba\Swivel\Config,
    \Zumba\Swivel\Bucket;

class ConfigTest extends \PHPUnit_Framework_TestCase {
    public function testConstructorAddsEmptyMap() {
        $config = new Config();
        $this->assertEmpty($config->getMap());
    }

    public function testConstructorAddsMapParam() {
        $map = ['Feature' => [1,2,3]];
        $config = new Config($map);
        $this->assertEquals($map, $config->getMap());
    }

    public function testAddFeature() {
        $config = new Config();
        $slug = 'Test';
        $buckets = [1,2];
        $config->addFeature($slug, $buckets);
        $this->assertEquals($buckets, $config->getMap()[$slug]);
    }

    public function testAddFeatures() {
        $config = new Config();
        $features = [ 'A' => [1,2,3], 'B' => [4,5,6] ];
        $config->addFeatures($features);
        $this->assertEquals($features, $config->getMap());
    }

    public function testFeaturesAreCumulative() {
        $config = new Config();
        $features = [ 'A' => [1,2,3] ];
        $config->addFeatures($features);
        $config->addFeature('A', [9]);
        $this->assertEquals([ 'A' => [1,2,3,9] ], $config->getMap());
    }

    public function testRemoveFeature() {
        $config = new Config();
        $features = [ 'A' => [1,2,3], 'B' => [4,5,6] ];
        $config->addFeatures($features);
        $config->removeFeature('B');
        $this->assertEquals([ 'A' => [1,2,3] ], $config->getMap());
    }

    public function testRemoveFeatureBucketOnly() {
        $config = new Config();
        $features = [ 'A' => [1,2,3], 'B' => [4,5,6] ];
        $config->addFeatures($features);
        $config->removeFeature('B', [5]);
        $this->assertEquals([ 'A' => [1,2,3], 'B' => [4,6] ], $config->getMap());
    }

    public function testGetBucket() {
        $index = 4;
        $config = new Config(['A' => [7,8,9]], 4);
        $bucket = $config->getBucket();
        $this->assertInstanceOf('Zumba\Swivel\Bucket', $bucket);
    }
}
