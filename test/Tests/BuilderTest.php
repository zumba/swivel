<?php

namespace Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use Zumba\Swivel\Builder;
use Zumba\Swivel\Map;
use Psr\Log\NullLogger;
use Zumba\Swivel\Behavior;
use Zumba\Swivel\Bucket;
use Zumba\Swivel\MetricsInterface;

class BuilderTest extends TestCase
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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setMethods(['enabled'])
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();
        $strategy = function () {
        };
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setConstructorArgs(['a', $strategy])
            ->getMock();

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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setMethods(['enabled'])
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior', 'setBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();

        $strategy = function () {
        };
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setConstructorArgs(['a', $strategy])
            ->getMock();

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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setMethods(['enabled'])
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();
        $value = null;
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setConstructorArgs([
                'a', function () use ($value) {
                    return $value;
                },
            ])
            ->getMock();

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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setMethods(['enabled'])
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior', 'setBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();

        $value = null;
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setConstructorArgs([
                'a', function () use ($value) {
                    return $value;
                },
            ])
            ->getMock();


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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();
        $strategy = function () {
        };
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setConstructorArgs([Builder::DEFAULT_SLUG, $strategy])
            ->getMock();

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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();
        $value = null;
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setConstructorArgs([
                Builder::DEFAULT_SLUG,
                function () use ($value) {
                    return $value;
                },
            ])
            ->getMock();

        $builder
            ->expects($this->once())
            ->method('getBehavior')
            ->with($this->isType('callable'))
            ->will($this->returnValue($behavior));

        $builder->setLogger(new NullLogger());
        $this->assertInstanceOf('Zumba\Swivel\BuilderInterface', $builder->defaultValue(null));
    }

    public function testDefaultBehaviorThrowsIfNoDefaultCalledFirst()
    {
        $this->expectException(LogicException::class);

        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $builder->setLogger(new NullLogger());
        $builder->noDefault();
        $builder->defaultBehavior(function () {
        });
    }

    public function testDefaultValueThrowsIfNoDefaultCalledFirst()
    {
        $this->expectException(LogicException::class);

        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $builder->setLogger(new NullLogger());
        $builder->noDefault();
        $builder->defaultValue('test');
    }

    public function testNoDefaultThrowsIfDefaultBehaviorDefined()
    {
        $this->expectException(LogicException::class);

        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();
        $strategy = function () {
        };
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setMethods(['getSlug'])
            ->setConstructorArgs([Builder::DEFAULT_SLUG, $strategy])
            ->getMock();

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

    public function testNoDefaultThrowsIfDefaultValueDefined()
    {
        $this->expectException(LogicException::class);

        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setMethods(['getBehavior'])
            ->setConstructorArgs(['Test', $bucket])
            ->getMock();
        $value = 'test';
        $behavior = $this->getMockBuilder(Behavior::class)
            ->setMethods(['getSlug'])
            ->setConstructorArgs([
                Builder::DEFAULT_SLUG,
                function () use ($value) {
                    return $value;
                },
            ])
            ->getMock();

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
        $map = new Map();
        $bucket = new Bucket($map);
        $builder = new Builder('Test', $bucket);
        $metrics = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();
        $builder->setMetrics($metrics);
        $builder->setLogger(new NullLogger());
        $builder->defaultBehavior(function () {
            return 'abc';

        });
        $this->assertSame('abc', $builder->execute());
    }

    public function testGetBehavior()
    {
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $strategy = function () {
        };

        $metrics = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();
        $builder->setMetrics($metrics);
        $builder->setLogger(new NullLogger());

        $behavior = $builder->getBehavior('a', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test'.Map::DELIMITER.'a', $behavior->getSlug());

        $behavior = $builder->getBehavior('', $strategy);
        $this->assertInstanceOf('Zumba\Swivel\Behavior', $behavior);
        $this->assertSame('Test', $behavior->getSlug());

    }

    public function testGetBehaviorProtectedMethod()
    {
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $strategy = [$this, 'protectedMethod'];

        $metrics = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();
        $builder->setMetrics($metrics);
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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $strategy = [$this, 'privateMethod'];

        $metrics = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();
        $builder->setMetrics($metrics);
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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $strategy = '\Tests\BuilderTest::protectedStaticMethod';

        $metrics = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();
        $builder->setMetrics($metrics);
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
        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $strategy = '\Tests\BuilderTest::privateStaticMethod';

        $metrics = $this->getMockBuilder(MetricsInterface::class)
            ->getMock();
        $builder->setMetrics($metrics);
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

    public function testGetBehaviorThrowsIfStrategyNotCallable()
    {
        $this->expectException(LogicException::class);

        $map = $this->getMockBuilder(Map::class)
            ->getMock();
        $bucket = $this->getMockBuilder(Bucket::class)
            ->setConstructorArgs([$map])
            ->getMock();
        $builder = new Builder('Test', $bucket);
        $builder->setLogger(new NullLogger());
        $behavior = $builder->getBehavior('a', null);
    }
}
