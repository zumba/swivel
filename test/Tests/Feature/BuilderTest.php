<?php
namespace Test\Feature;

use \Zumba\Swivel\Feature\Builder;
use \Zumba\Swivel\Feature;

class BuilderTest extends \PHPUnit_Framework_TestCase {
    public function testAddBehavior() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Feature\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function() {};
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], ['a', $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with('a', $strategy)
            ->will($this->returnValue($behavior));

        $bucket
            ->expects($this->once())
            ->method('enabled')
            ->with($behavior);

        $this->assertInstanceOf('Zumba\Swivel\Feature\BuilderInterface', $builder->addBehavior('a', $strategy));
    }

    public function testDefaultBehavior() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Feature\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function() {};
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], [Builder::DEFAULT_SLUG, $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with($strategy)
            ->will($this->returnValue($behavior));

        $this->assertInstanceOf('Zumba\Swivel\Feature\BuilderInterface', $builder->defaultBehavior($strategy));
    }

    /**
     * @expectedException \LogicException
     */
    public function testDefaultBehaviorThrowsIfNoDefaultCalledFirst() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = new Builder('Test', $bucket);
        $builder->noDefault();
        $builder->defaultBehavior(function() {});
    }

    /**
     * @expectedException \LogicException
     */
    public function testNoDefaultThrowsIfDefaultBehaviorDefined() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Feature\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function() {};
        $behavior = $this->getMock('Zumba\Swivel\Behavior', ['getSlug'], [Builder::DEFAULT_SLUG, $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->will($this->returnValue($behavior));

        $behavior
            ->expects($this->once())
            ->method('getSlug')
            ->will($this->returnValue(Builder::DEFAULT_SLUG));

        $builder->defaultBehavior($strategy);
        $builder->noDefault();

    }

    public function testExecute() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Feature\Builder', ['getFeature'], ['Test', $bucket]);
        $feature = $this->getMock('Zumba\Swivel\Feature');

        $builder
            ->expects($this->once())
            ->method('getFeature')
            ->will($this->returnValue($feature));

        $feature
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('abc'));

        $this->assertSame('abc', $builder->execute());
    }

    public function testGetFeature() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Feature\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function() {};
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], ['a', $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with('a', $strategy)
            ->will($this->returnValue($behavior));

        $bucket
            ->expects($this->once())
            ->method('enabled')
            ->with($behavior)
            ->will($this->returnValue(true));

        $feature = $builder->addBehavior('a', $strategy)->getFeature();
        $this->assertInstanceOf('Zumba\Swivel\Feature', $feature);
    }

    public function testGetBehavior() {
        $map = $this->getMock('Zumba\Swivel\Feature\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', [], [$map]);
        $builder = new Builder('Test', $bucket, $slug);
        $strategy = function() {};

        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test' . Feature::DELIMITER . 'a', $behavior->getSlug());
    }
}
