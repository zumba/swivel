<?php
namespace Tests\Integration;

use \Zumba\Swivel\Config;
use \Zumba\Swivel\Manager;

class SwivelTest extends \PHPUnit_Framework_TestCase {

    protected $map;

    protected function setUp() {
        $this->map = [
            'System' => [1,2,3,4,5],
            'System.NewAlgorithm' => [1,2,3,4,5],
            'OldFeature' => [1,2,3,4,5,6,7,8,9,10],
            'OldFeature.Legacy' => [1,2,3,4,5,6,7,8,9,10],
            'BadIdea' => [],
            'BadIdea.Implementation' => [],
            'ParentOff' => [],
            'ParentOff.ChildOn' => [1],
            'NewFeature' => [6,7,8],
            'NewFeature.SimpleStuff' => [8],
            'NewFeature.ComplexStuff' => [6,7],
            'NewFeature.ComplexStuff.VersionA' => [6],
            'NewFeature.ComplexStuff.VersionB' => [7]
        ];
    }

    public function testSystemNewAlgorithmValidBucket() {
        $swivel = new Manager(new Config($this->map, 4));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', function() { return 'NewHotness'; })
            ->defaultBehavior(function() { return 'OldAndBusted'; })
            ->execute();

        $this->assertSame('NewHotness', $result);
    }

    public function testSystemNewAlgorithmInvalidBucket() {
        $swivel = new Manager(new Config($this->map, 9));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', function() { return 'NewHotness'; })
            ->defaultBehavior(function() { return 'OldAndBusted'; })
            ->execute();

        $this->assertSame('OldAndBusted', $result);
    }

    public function testSystemNewAlgorithmLiteral() {
        $swivel = new Manager(new Config($this->map, 3));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', 'NewHotness')
            ->defaultBehavior('OldAndBusted')
            ->execute();

        $this->assertSame('NewHotness', $result);
    }

    /**
     * @dataProvider allBucketProvider
     */
    public function testDefaultBehaviorNeverCalledWhenAllBucketsOn($bucket) {
        $swivel = new Manager(new Config($this->map, $bucket));
        $result = $swivel->forFeature('OldFeature')
            ->addBehavior('Legacy', 'AlwaysOn')
            ->defaultBehavior('NeverOn')
            ->execute();

        $this->assertSame('AlwaysOn', $result);
    }

    /**
     * @dataProvider allBucketProvider
     */
    public function testNewBehaviorNeverCalledWhenAllBucketsOff($bucket) {
        $swivel = new Manager(new Config($this->map, $bucket));
        $result = $swivel->forFeature('BadIdea')
            ->addBehavior('Implementation', 'IsOn?')
            ->defaultBehavior('NeverOn')
            ->execute();

        $this->assertSame('NeverOn', $result);
    }

    public function testDisableParentKillsChild() {
        $swivel = new Manager(new Config($this->map, 1));
        $result = $swivel->forFeature('ParentOff')
            ->addBehavior('ChildOn', 'NeverWorks')
            ->defaultBehavior('AlwaysDefault')
            ->execute();

        $this->assertSame('AlwaysDefault', $result);
    }

    /**
     * @dataProvider branchingChildrenProvider
     */
    public function testBranchingChildren($bucket, $assertOne, $assertTwo, $assertThree, $assertFour) {
        $on = function() { return true; };
        $off = function() { return false; };
        $swivel = new Manager(new Config($this->map, $bucket));
        $result = $swivel->forFeature('NewFeature')
            ->addBehavior('SimpleStuff', $on)
            ->defaultBehavior($off)
            ->execute();

        $this->$assertOne($result);

        $result = $swivel->forFeature('NewFeature')
            ->addBehavior('ComplexStuff', $on)
            ->defaultBehavior($off)
            ->execute();

        $this->$assertTwo($result);

        $result = $swivel->forFeature('NewFeature')
            ->addBehavior('ComplexStuff.VersionA', $on)
            ->defaultBehavior($off)
            ->execute();

        $this->$assertThree($result);

        $result = $swivel->forFeature('NewFeature')
            ->addBehavior('ComplexStuff.VersionB', $on)
            ->defaultBehavior($off)
            ->execute();

        $this->$assertFour($result);
    }

    public function testInvokeSystemNewAlgorithmValidBucket() {
        $swivel = new Manager(new Config($this->map, 1));
        $result = $swivel->invoke('System.NewAlgorithm', 'NewHotness', 'OldAndBusted');
        $this->assertSame('NewHotness', $result);
    }

    public function testInvokeSystemNewAlgorithmInvalidBucket() {
        $swivel = new Manager(new Config($this->map, 10));
        $result = $swivel->invoke('System.NewAlgorithm', 'NewHotness', 'OldAndBusted');
        $this->assertSame('OldAndBusted', $result);
    }

    public function testInvokeSystemNewAlgorithmValidBucketNoDefault() {
        $swivel = new Manager(new Config($this->map, 1));
        $result = $swivel->invoke('System.NewAlgorithm', 'NewHotness');
        $this->assertSame('NewHotness', $result);
    }

    public function testInvokeSystemNewAlgorithmInvalidBucketNoDefault() {
        $swivel = new Manager(new Config($this->map, 10));
        $result = $swivel->invoke('System.NewAlgorithm', 'NewHotness');
        $this->assertSame(null, $result);
    }

    public function testNoDefaultFeatureOn() {
        $swivel = new Manager(new Config($this->map, 2));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', 'NewHotness')
            ->noDefault()
            ->execute();

        $this->assertSame('NewHotness', $result);
    }

    public function testNoDefaultFeatureOff() {
        $swivel = new Manager(new Config($this->map, 8));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', 'NewHotness')
            ->noDefault()
            ->execute();

        $this->assertSame(null, $result);
    }

    /**
     * @dataProvider falseyValueProvider
     */
    public function testShorthandFalseyValuesAllowed($falseyValue) {
        $swivel = new Manager(new Config($this->map, 10));
        $this->assertSame($falseyValue, $swivel->invoke('OldFeature.Legacy', $falseyValue));
    }

    public function allBucketProvider() {
        return [ [1], [2], [3], [4], [5], [6], [7], [8], [9], [10] ];
    }

    public function branchingChildrenProvider() {
        return [
            [ 6, 'assertFalse', 'assertTrue', 'assertTrue', 'assertFalse' ],
            [ 7, 'assertFalse', 'assertTrue', 'assertFalse', 'assertTrue' ],
            [ 8, 'assertTrue', 'assertFalse', 'assertFalse', 'assertFalse' ]
        ];
    }

    public function falseyValueProvider() {
        return [ [0], [[]], [null], [''], [false] ];
    }
}
