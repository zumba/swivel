<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Zumba\Swivel\Config;
use Zumba\Swivel\Manager;

class SwivelTest extends TestCase
{
    protected $map;

    protected function setUp(): void
    {
        $this->map = [
            'System' => [1, 2, 3, 4, 5],
            'System.NewAlgorithm' => [1, 2, 3, 4, 5],
            'OldFeature' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            'OldFeature.Legacy' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            'BadIdea' => [],
            'BadIdea.Implementation' => [],
            'ParentOff' => [],
            'ParentOff.ChildOn' => [1],
            'NewFeature' => [6, 7, 8],
            'NewFeature.SimpleStuff' => [8],
            'NewFeature.ComplexStuff' => [6, 7],
            'NewFeature.ComplexStuff.VersionA' => [6],
            'NewFeature.ComplexStuff.VersionB' => [7],
        ];
    }

    public function testSystemNewAlgorithmValidBucket()
    {
        $swivel = new Manager(new Config($this->map, 4));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', fn() => 'NewHotness')
            ->defaultBehavior(fn() => 'OldAndBusted')
            ->execute();

        $this->assertSame('NewHotness', $result);
    }

    public function testSystemNewAlgorithmInvalidBucket()
    {
        $swivel = new Manager(new Config($this->map, 9));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', fn() => 'NewHotness')
            ->defaultBehavior(fn() => 'OldAndBusted')
            ->execute();

        $this->assertSame('OldAndBusted', $result);
    }

    public function testSystemNewAlgorithmValue()
    {
        $swivel = new Manager(new Config($this->map, 3));
        $result = $swivel->forFeature('System')
            ->addValue('NewAlgorithm', 'NewHotness')
            ->defaultValue('OldAndBusted')
            ->execute();

        $this->assertSame('NewHotness', $result);
    }

    /**
     * @dataProvider allBucketProvider
     */
    public function testDefaultBehaviorNeverCalledWhenAllBucketsOn($bucket)
    {
        $swivel = new Manager(new Config($this->map, $bucket));
        $result = $swivel->forFeature('OldFeature')
            ->addBehavior('Legacy', fn() => 'AlwaysOn')
            ->defaultBehavior(fn() => 'NeverOn')
            ->execute();

        $this->assertSame('AlwaysOn', $result);
    }

    /**
     * @dataProvider allBucketProvider
     */
    public function testNewBehaviorNeverCalledWhenAllBucketsOff($bucket)
    {
        $swivel = new Manager(new Config($this->map, $bucket));
        $result = $swivel->forFeature('BadIdea')
            ->addBehavior('Implementation', fn() => 'IsOn?')
            ->defaultBehavior(fn() => 'NeverOn')
            ->execute();

        $this->assertSame('NeverOn', $result);
    }

    public function testDisableParentKillsChild()
    {
        $swivel = new Manager(new Config($this->map, 1));
        $result = $swivel->forFeature('ParentOff')
            ->addBehavior('ChildOn', fn() => 'NeverWorks')
            ->defaultBehavior(fn() => 'AlwaysDefault')
            ->execute();

        $this->assertSame('AlwaysDefault', $result);
    }

    /**
     * @dataProvider branchingChildrenProvider
     */
    public function testBranchingChildren($bucket, $assertOne, $assertTwo, $assertThree, $assertFour)
    {
        $on = fn() => true;
        $off = fn() => false;
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

    public function testInvokeSystemNewAlgorithmValidBucket()
    {
        $swivel = new Manager(new Config($this->map, 1));
        $result = $swivel->invoke(
            'System.NewAlgorithm',
            fn() => 'NewHotness',
            fn() => 'OldAndBusted'
        );
        $this->assertSame('NewHotness', $result);
    }

    public function testInvokeSystemNewAlgorithmInvalidBucket()
    {
        $swivel = new Manager(new Config($this->map, 10));
        $result = $swivel->invoke(
            'System.NewAlgorithm',
            fn() => 'NewHotness',
            fn() => 'OldAndBusted'
        );
        $this->assertSame('OldAndBusted', $result);
    }

    public function testInvokeSystemNewAlgorithmValidBucketNoDefault()
    {
        $swivel = new Manager(new Config($this->map, 1));
        $result = $swivel->invoke('System.NewAlgorithm', fn() => 'NewHotness');
        $this->assertSame('NewHotness', $result);
    }

    public function testInvokeSystemNewAlgorithmInvalidBucketNoDefault()
    {
        $swivel = new Manager(new Config($this->map, 10));
        $result = $swivel->invoke('System.NewAlgorithm', fn() => 'NewHotness');
        $this->assertSame(null, $result);
    }

    public function testNoDefaultFeatureOn()
    {
        $swivel = new Manager(new Config($this->map, 2));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', fn() => 'NewHotness')
            ->noDefault()
            ->execute();

        $this->assertSame('NewHotness', $result);
    }

    public function testNoDefaultFeatureOff()
    {
        $swivel = new Manager(new Config($this->map, 8));
        $result = $swivel->forFeature('System')
            ->addBehavior('NewAlgorithm', fn() => 'NewHotness')
            ->noDefault()
            ->execute();

        $this->assertSame(null, $result);
    }

    /**
     * @dataProvider falseyValueProvider
     */
    public function testFalseyValuesAllowedInBehaviors($falseyValue)
    {
        $swivel = new Manager(new Config($this->map, 10));
        $this->assertSame($falseyValue, $swivel->invoke('OldFeature.Legacy', fn() => $falseyValue));
    }

    /**
     * @dataProvider falseyValueProvider
     */
    public function testFalseyValuesAllowed($falseyValue)
    {
        $swivel = new Manager(new Config($this->map, 10));
        $this->assertSame($falseyValue, $swivel->returnValue('OldFeature.Legacy', $falseyValue));
    }

    public function allBucketProvider()
    {
        return [[1], [2], [3], [4], [5], [6], [7], [8], [9], [10]];
    }

    public function branchingChildrenProvider()
    {
        return [
            [6, 'assertFalse', 'assertTrue', 'assertTrue', 'assertFalse'],
            [7, 'assertFalse', 'assertTrue', 'assertFalse', 'assertTrue'],
            [8, 'assertTrue', 'assertFalse', 'assertFalse', 'assertFalse'],
        ];
    }

    public function falseyValueProvider()
    {
        return [[0], [[]], [null], [''], [false]];
    }

    /**
     * Every slug shape invoke() and returnValue() can receive, resolved end to end
     * through Config, Map and Bucket. Both methods must agree on every case.
     *
     * @dataProvider slugShapeProvider
     */
    public function testSlugShapes(string $slug, int $bucket, bool $expectedOn, string $why)
    {
        $swivel = new Manager(new Config($this->map, $bucket));
        $expected = $expectedOn ? 'on' : 'off';

        $this->assertSame($expected, $swivel->returnValue($slug, 'on', 'off'), "returnValue: $why");
        $this->assertSame($expected, $swivel->invoke($slug, fn() => 'on', fn() => 'off'), "invoke: $why");
    }

    public function slugShapeProvider(): array
    {
        return [
            'bare slug, bucket in'           => ['NewFeature', 7, true, 'a bare slug toggles the feature itself'],
            'bare slug, bucket out'          => ['NewFeature', 2, false, 'a bare slug respects the bucket'],
            'bare slug, all buckets on'      => ['OldFeature', 10, true, 'a bare slug on an always-on feature'],
            'bare slug, all buckets off'     => ['BadIdea', 1, false, 'a bare slug on an always-off feature'],
            'one level, bucket in'           => ['System.NewAlgorithm', 4, true, 'Feature.behavior form'],
            'one level, bucket out'          => ['System.NewAlgorithm', 9, false, 'Feature.behavior form respects the bucket'],
            'two levels, bucket in'          => [
                'NewFeature.ComplexStuff.VersionA', 6, true, 'the behavior part may itself contain dots'
            ],
            'two levels, sibling bucket'     => [
                'NewFeature.ComplexStuff.VersionA', 7, false, 'VersionA is bucket 6 only'
            ],
            'two levels, other sibling'      => ['NewFeature.ComplexStuff.VersionB', 7, true, 'VersionB is bucket 7 only'],
            'parent off kills child'         => ['ParentOff.ChildOn', 1, false, 'a child cannot be on when its parent is off'],
            'trailing delimiter'             => ['NewFeature.', 7, true, 'an empty behavior part means the feature itself'],
            'leading delimiter'              => ['.NewFeature', 7, false, 'an empty feature part is not a feature'],
            'empty slug'                     => ['', 7, false, 'an empty slug is simply off, never an error'],
            'double delimiter'               => ['NewFeature..SimpleStuff', 8, false, 'an empty middle segment is not a feature'],
            'unknown bare slug'              => ['Unknown', 1, false, 'a slug missing from the map is off'],
            'unknown behavior'               => ['System.Unknown', 4, false, 'a missing behavior under a known feature is off'],
            'unknown feature, known-looking' => [
                'Unknown.NewAlgorithm', 4, false, 'a missing feature is off even if the behavior name exists elsewhere'
            ],
        ];
    }

    /**
     * A bare slug follows the feature's bucket list across every bucket.
     *
     * @dataProvider allBucketProvider
     */
    public function testBareSlugAcrossAllBuckets(int $bucket)
    {
        $swivel = new Manager(new Config($this->map, $bucket));
        $expected = in_array($bucket, $this->map['NewFeature'], true) ? 'on' : 'off';

        $this->assertSame($expected, $swivel->returnValue('NewFeature', 'on', 'off'));
        $this->assertSame($expected, $swivel->invoke('NewFeature', fn() => 'on', fn() => 'off'));
    }

    /**
     * The missing-slug callback is told about unknown slugs in the form the map
     * would need, for both the bare and the dotted form, and stays silent for known ones.
     *
     * @dataProvider missingSlugProvider
     */
    public function testMissingSlugCallbackThroughInvokeAndReturnValue(string $slug, array $expectedCallbacks)
    {
        $seen = [];
        $swivel = new Manager(new Config($this->map, 1, null, function ($missing) use (&$seen) {
            $seen[] = $missing;
        }));

        $swivel->returnValue($slug, 'on', 'off');
        $this->assertSame($expectedCallbacks, $seen, 'returnValue');

        $seen = [];
        $swivel->invoke($slug, fn() => 'on', fn() => 'off');
        $this->assertSame($expectedCallbacks, $seen, 'invoke');
    }

    public function missingSlugProvider(): array
    {
        return [
            'known bare slug'     => ['NewFeature', []],
            'known dotted slug'   => ['System.NewAlgorithm', []],
            'unknown bare slug'   => ['Unknown', ['Unknown']],
            'unknown dotted slug' => ['System.Unknown', ['System.Unknown']],
            'unknown feature'     => ['Unknown.Behavior', ['Unknown.Behavior']],
            'empty slug'          => ['', ['']],
        ];
    }

    /**
     * invoke() only runs the callable for the branch that is taken, for bare and dotted slugs alike.
     *
     * @dataProvider lazyInvokeProvider
     */
    public function testInvokeRunsOnlyTheTakenBranch(string $slug, int $bucket, bool $expectedOn)
    {
        $swivel = new Manager(new Config($this->map, $bucket));
        $calls = ['a' => 0, 'b' => 0];

        $result = $swivel->invoke(
            $slug,
            function () use (&$calls) {
                $calls['a']++;
                return 'a';
            },
            function () use (&$calls) {
                $calls['b']++;
                return 'b';
            }
        );

        $this->assertSame($expectedOn ? 'a' : 'b', $result);
        $this->assertSame($expectedOn ? ['a' => 1, 'b' => 0] : ['a' => 0, 'b' => 1], $calls);
    }

    public function lazyInvokeProvider(): array
    {
        return [
            'bare on'    => ['NewFeature', 8, true],
            'bare off'   => ['NewFeature', 1, false],
            'dotted on'  => ['NewFeature.SimpleStuff', 8, true],
            'dotted off' => ['NewFeature.SimpleStuff', 6, false],
        ];
    }

    /**
     * Without a default, invoke() and returnValue() yield null when the feature is off.
     *
     * @dataProvider noDefaultProvider
     */
    public function testNoDefaultYieldsNullWhenOff(string $slug)
    {
        $swivel = new Manager(new Config($this->map, 1));

        $this->assertNull($swivel->returnValue($slug, 'on'));
        $this->assertNull($swivel->invoke($slug, fn() => 'on'));
    }

    public function noDefaultProvider(): array
    {
        return [
            'bare'   => ['NewFeature'],
            'dotted' => ['NewFeature.SimpleStuff'],
            'unknown' => ['Unknown'],
        ];
    }

    /**
     * A falsy enabled value is returned as is for a bare slug, never mistaken for "off".
     *
     * @dataProvider falseyValueProvider
     */
    public function testFalseyValuesAllowedForBareSlug($falseyValue)
    {
        $swivel = new Manager(new Config($this->map, 7));

        $this->assertSame($falseyValue, $swivel->returnValue('NewFeature', $falseyValue, 'default'));
        $this->assertSame($falseyValue, $swivel->invoke('NewFeature', fn() => $falseyValue, fn() => 'default'));
    }
}
