<?php

namespace Zumba\Swivel;

class Builder implements BuilderInterface
{
    use \Psr\Log\LoggerAwareTrait;

    const DEFAULT_SLUG = '__swivel_default';
    const DEFAULT_STRATEGY = '__swivel_default_strategy';

    /**
     * Arguments to be passed to the behavior.
     */
    protected array $args = [];

    /**
     * The behavior to be executed.
     */
    protected ?BehaviorInterface $behavior = null;

    /**
     * The user's Bucket.
     */
    protected BucketInterface $bucket;

    /**
     * Whether this feature requires a default behavior.
     */
    protected bool $defaultWaived = false;

    /**
     * Keys used in metrics.
     */
    private array $keys = [
        'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'FIFTH',
        'SIXTH', 'SEVENTH', 'EIGHTH', 'NINTH', 'TENTH',
    ];

    /**
     * A Metrics object.
     */
    protected ?MetricsInterface $metrics = null;

    /**
     * Parent Feature slug.
     */
    protected string $slug;

    /**
     * Zumba\Swivel\Builder.
     */
    public function __construct(string $slug, BucketInterface $bucket)
    {
        $this->slug = $slug;
        $this->bucket = $bucket;
    }

    /**
     * Add a behavior to be executed later.
     *
     * Behavior will only be added if it is enabled for the user's bucket.
     */
    public function addBehavior(string $slug, mixed $strategy, array $args = []): BuilderInterface
    {
        $behavior = $this->getBehavior($slug, $strategy);
        if ($this->bucket->enabled($behavior)) {
            $this->setBehavior($behavior, $args);
        }

        return $this;
    }

    /**
     * Add a value to be returned when the builder is executed.
     *
     * Value will only be returned if it is enabled for the user's bucket.
     */
    public function addValue(string $slug, mixed $value): BuilderInterface
    {
        $behavior = $this->getBehavior($slug, fn() => $value);
        if ($this->bucket->enabled($behavior)) {
            $this->setBehavior($behavior);
        }

        return $this;
    }

    /**
     * Add a default behavior.
     *
     * Will be used if all other behaviors and values are not enabled for the user's bucket.
     */
    public function defaultBehavior(mixed $strategy, array $args = []): BuilderInterface
    {
        if ($this->defaultWaived) {
            $exception = new \LogicException('Defined a default behavior after `noDefault` was called.');
            $this->logger->critical('Swivel', compact('exception'));
            throw $exception;
        }
        if (!$this->behavior) {
            $this->setBehavior($this->getBehavior($strategy), $args);
        }

        return $this;
    }

    /**
     * Add a default value.
     *
     * Will be used if all other behaviors and values are not enabled for the user's bucket.
     */
    public function defaultValue(mixed $value): BuilderInterface
    {
        if ($this->defaultWaived) {
            $exception = new \LogicException('Defined a default value after `noDefault` was called.');
            $this->logger->critical('Swivel', compact('exception'));
            throw $exception;
        }
        if (!$this->behavior) {
            $this->setBehavior($this->getBehavior(fn() => $value));
        }

        return $this;
    }

    /**
     * Execute the feature.
     */
    public function execute(): mixed
    {
        $behavior = $this->behavior ?: $this->getBehavior(fn() => null);
        $behaviorSlug = $behavior->getSlug();

        $this->metrics && $this->startMetrics($behaviorSlug);
        $result = $behavior->execute($this->args ?: []);
        $this->metrics && $this->stopMetrics($behaviorSlug);

        return $result;
    }

    /**
     * Create and return a new Behavior.
     *
     * The $strategy parameter must be a valid callable.
     */
    public function getBehavior(string|callable $slug, mixed $strategy = self::DEFAULT_STRATEGY): BehaviorInterface
    {
        $this->logger->debug('Swivel - Creating new behavior.', compact('slug'));
        if ($strategy === static::DEFAULT_STRATEGY) {
            $strategy = $slug;
            $slug = static::DEFAULT_SLUG;
        }

        if (!is_callable($strategy)) {
            if (is_string($strategy)) {
                $strategy = explode('::', $strategy);
            }
            if (!isset($strategy[0], $strategy[1]) || !method_exists($strategy[0], $strategy[1])) {
                throw new \LogicException('Invalid callable passed to Zumba\Swivel\Builder::getBehavior');
            }
            $closure = fn() => $strategy(...func_get_args());
            $strategy = $closure->bindTo(null, $strategy[0]);
        }
        $slug = empty($slug) ? $this->slug : $this->slug.Map::DELIMITER.$slug;

        return new Behavior($slug, $strategy, $this->logger);
    }

    /**
     * Waive the default behavior for this feature.
     */
    public function noDefault(): BuilderInterface
    {
        if ($this->behavior && $this->behavior->getSlug() === static::DEFAULT_SLUG) {
            $exception = new \LogicException('Called `noDefault` after a default behavior was defined.');
            $this->logger->critical('Swivel', compact('exception'));
            throw $exception;
        }
        $this->defaultWaived = true;

        return $this;
    }

    /**
     * Set the behavior and it's args.
     */
    protected function setBehavior(BehaviorInterface $behavior, array $args = []): void
    {
        $slug = $behavior->getSlug();
        $this->logger->debug('Swivel - Setting behavior.', compact('slug', 'args'));
        $this->behavior = $behavior;
        $this->args = $args;
    }

    /**
     * Set a metrics object.
     */
    public function setMetrics(MetricsInterface $metrics): void
    {
        $this->metrics = $metrics;
    }

    /**
     * Start collecting metrics about this feature.
     */
    protected function startMetrics(string $behaviorSlug): void
    {
        $metrics = $this->metrics;
        $bucketIndex = $this->bucket->getIndex();

        // Increment counters
        $metrics->increment('Features', $behaviorSlug);
        $metrics->increment('Buckets', $this->keys[$bucketIndex - 1], $behaviorSlug);

        // Start timers
        $metrics->startTiming('Features', $behaviorSlug);
        $metrics->startMemoryProfile('Features', $behaviorSlug);
    }

    /**
     * Stop collecting metrics about this feature.
     */
    protected function stopMetrics(string $behaviorSlug): void
    {
        $metrics = $this->metrics;
        $metrics->endMemoryProfile('Features', $behaviorSlug);
        $metrics->endTiming('Features', $behaviorSlug);
    }
}
