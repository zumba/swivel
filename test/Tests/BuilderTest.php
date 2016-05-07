<?php

namespace Tests;

use Zumba\Swivel\Builder;
use Zumba\Swivel\Map;
use Psr\Log\NullLogger;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Used for testing that protected method is called with proper arguments.
     *
     * @param string $arg1
     * @param string $arg2
     *
     * @return string Args concatenated together
     */
    protected function protectedMethod($arg1, $arg2)
    {
        return $arg1.$arg2;
    }

    /**
     * Used for testing that private method is called with proper arguments.
     *
     * @param string $arg1
     * @param string $arg2
     *
     * @return string Args concatenated together
     */
    private function privateMethod($arg1, $arg2)
    {
        return $arg1.$arg2;
    }

    /**
     * Used for testing that protected static method is called with proper arguments.
     *
     * @param string $arg1
     * @param string $arg2
     *
     * @return string Args concatenated together
     */
    protected static function protectedStaticMethod($arg1, $arg2)
    {
        return $arg1.$arg2;
    }

    /**
     * Used for testing that private static method is called with proper arguments.
     *
     * @param string $arg1
     * @param string $arg2
     *
     * @return string Args concatenated together
     */
    private static function privateStaticMethod($arg1, $arg2)
    {
        return $arg1.$arg2;
    }

    public function testAddBehaviorNotEnabled()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', ['enabled'], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function () {
        };
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
            ->will($this->returnValue(false));

        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->addBehavior('a', $strategy));
    }

    public function testAddBehaviorEnabled()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', ['enabled'], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior', 'setBehavior'], ['Test', $bucket]);
        $strategy = function () {
        };
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], ['a', $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with('a', $strategy)
            ->will($this->returnValue($behavior));

        $builder
            ->expects($this->once())
            ->method('setBehavior')
            ->with($behavior, []);

        $bucket
            ->expects($this->once())
            ->method('enabled')
            ->with($behavior)
            ->will($this->returnValue(true));

        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->addBehavior('a', $strategy));
    }

    public function testAddValueNotEnabled()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', ['enabled'], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior'], ['Test', $bucket]);
        $value = null;
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], [
            'a', function () use ($value) {
                return $value;
            },
        ]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with('a', $this->isType('callable'))
            ->will($this->returnValue($behavior));

        $bucket
            ->expects($this->once())
            ->method('enabled')
            ->with($behavior)
            ->will($this->returnValue(false));

        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->addValue('a', $value));
    }

    public function testAddValueEnabled()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', ['enabled'], [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior', 'setBehavior'], ['Test', $bucket]);
        $value = null;
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], [
            'a', function () use ($value) {
                return $value;
            },
        ]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with('a', $this->isType('callable'))
            ->will($this->returnValue($behavior));

        $builder
            ->expects($this->once())
            ->method('setBehavior')
            ->with($behavior, []);

        $bucket
            ->expects($this->once())
            ->method('enabled')
            ->with($behavior)
            ->will($this->returnValue(true));

        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->addValue('a', $value));
    }

    public function testDefaultBehavior()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function () {
        };
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], [Builder::DEFAULT_SLUG, $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with($strategy)
            ->will($this->returnValue($behavior));

        $builder->setLogger(new NullLogger());
        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->defaultBehavior($strategy));
    }

    public function testDefaultValue()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior'], ['Test', $bucket]);
        $value = null;
        $behavior = $this->getMock('Zumba\Swivel\Behavior', [], [
            Builder::DEFAULT_SLUG,
            function () use ($value) {
                return $value;
            },
        ]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with($this->isType('callable'))
            ->will($this->returnValue($behavior));

        $builder->setLogger(new NullLogger());
        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->defaultValue(null));
    }

    /**
     * @expectedException \LogicException
     */
    public function testDefaultBehaviorThrowsIfNoDefaultCalledFirst()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $builder->setLogger(new NullLogger());
        $builder->noDefault();
        $builder->defaultBehavior(function () {
        });
    }

    /**
     * @expectedException \LogicException
     */
    public function testDefaultValueThrowsIfNoDefaultCalledFirst()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $builder->setLogger(new NullLogger());
        $builder->noDefault();
        $builder->defaultValue('test');
    }

    /**
     * @expectedException \LogicException
     */
    public function testNoDefaultThrowsIfDefaultBehaviorDefined()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior'], ['Test', $bucket]);
        $strategy = function () {
        };
        $behavior = $this->getMock('Zumba\Swivel\Behavior', ['getSlug'], [Builder::DEFAULT_SLUG, $strategy]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->will($this->returnValue($behavior));

        $behavior
            ->expects($this->exactly(2))
            ->method('getSlug')
            ->will($this->returnValue(Builder::DEFAULT_SLUG));

        $builder->setLogger(new NullLogger());
        $builder->defaultBehavior($strategy);
        $builder->noDefault();
    }

    /**
     * @expectedException \LogicException
     */
    public function testNoDefaultThrowsIfDefaultValueDefined()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', ['getBehavior'], ['Test', $bucket]);
        $value = 'test';
        $behavior = $this->getMock('Zumba\Swivel\Behavior', ['getSlug'], [
            Builder::DEFAULT_SLUG,
            function () use ($value) {
                return $value;
            },
        ]);

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->will($this->returnValue($behavior));

        $behavior
            ->expects($this->exactly(2))
            ->method('getSlug')
            ->will($this->returnValue(Builder::DEFAULT_SLUG));

        $builder->setLogger(new NullLogger());
        $builder->defaultValue($value);
        $builder->noDefault();
    }

    public function testExecute()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = $this->getMock('Zumba\Swivel\Builder', null, ['Test', $bucket]);
        $builder->setMetrics($this->getMock('Zumba\Swivel\MetricsInterface'));
        $builder->setLogger(new NullLogger());
        $builder->defaultBehavior(function () {
            return 'abc';

        });
        $this->assertSame('abc', $builder->execute());
    }

    public function testGetBehavior()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $strategy = function () {
        };

        $builder->setMetrics($this->getMock('Zumba\Swivel\MetricsInterface'));
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test'.Map::DELIMITER.'a', $behavior->getSlug());
    }

    public function testGetBehaviorProtectedMethod()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $strategy = [$this, 'protectedMethod'];

        $builder->setMetrics($this->getMock('Zumba\Swivel\MetricsInterface'));
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test'.Map::DELIMITER.'a', $behavior->getSlug());

        $this->assertEquals(
            'ArgaArgb',
            $behavior->execute(['Arga', 'Argb']),
            'Test that the protected method is able to be called'
        );
    }

    public function testGetBehaviorPrivateMethod()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $strategy = [$this, 'privateMethod'];

        $builder->setMetrics($this->getMock('Zumba\Swivel\MetricsInterface'));
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test'.Map::DELIMITER.'a', $behavior->getSlug());

        $this->assertEquals(
            'ArgaArgb',
            $behavior->execute(['Arga', 'Argb']),
            'Test that the private method is able to be called'
        );
    }

    public function testGetBehaviorProtectedStaticMethod()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $strategy = '\Tests\BuilderTest::protectedStaticMethod';

        $builder->setMetrics($this->getMock('Zumba\Swivel\MetricsInterface'));
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test'.Map::DELIMITER.'a', $behavior->getSlug());

        $this->assertEquals(
            'ArgaArgb',
            $behavior->execute(['Arga', 'Argb']),
            'Test that the protected static method is able to be called'
        );
    }

    public function testGetBehaviorPrivateStaticMethod()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $strategy = '\Tests\BuilderTest::privateStaticMethod';

        $builder->setMetrics($this->getMock('Zumba\Swivel\MetricsInterface'));
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test'.Map::DELIMITER.'a', $behavior->getSlug());

        $this->assertEquals(
            'ArgaArgb',
            $behavior->execute(['Arga', 'Argb']),
            'Test that the private static method is able to be called'
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetBehaviorThrowsIfStrategyNotCallable()
    {
        $map = $this->getMock('Zumba\Swivel\Map');
        $bucket = $this->getMock('Zumba\Swivel\Bucket', null, [$map]);
        $builder = new Builder('Test', $bucket);
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', null);
    }
}
