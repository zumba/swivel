<?php
namespace Zumba\Swivel;

use \Zumba\Swivel\BucketInterface,
    \Zumba\Swivel\Behavior,
    \Psr\Log\LoggerInterface,
    \Zumba\Swivel\Logging\NullLogger;

class Builder implements BuilderInterface {

    use \Psr\Log\LoggerAwareTrait;

    const DEFAULT_SLUG = '__swivel_default';
    const DEFAULT_STRATEGY = '__swivel_default_strategy';

    /**
     * Arguments to be passed to the behavior
     *
     * @var array
     */
    protected $args;

    /**
     * The behavior to be executed.
     *
     * @var \Zumba\Swivel\Behavior
     */
    protected $behavior;

    /**
     * The user's Bucket.
     *
     * @var Zumba\Swivel\BucketInterface
     */
    protected $bucket;

    /**
     * Whether this feature requires a default behavior.
     *
     * @var boolean
     */
    protected $defaultWaived;


    /**
     * Keys used in metrics
     *
     * @var array
     */
    private $keys = [
        'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'FIFTH',
        'SIXTH', 'SEVENTH', 'EIGHTH', 'NINTH', 'TENTH'
    ];

    /**
     * A Metrics object.
     *
     * @var \Zumba\Swivel\MetricsInterface
     */
    protected $metrics;

    /**
     * Parent Feature slug
     *
     * @var string
     */
    protected $slug;

    /**
     * Zumba\Swivel\Builder
     *
     * @param string $slug
     * @param BucketInterface $bucket
     */
    public function __construct($slug, BucketInterface $bucket) {
        $this->slug = $slug;
        $this->bucket = $bucket;
    }

    /**
     * Add a behavior to be executed later.
     *
     * Behavior will only be added if it is enabled for the user's bucket.
     *
     * @param string $slug
     * @param mixed $strategy
     * @param array $args
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function addBehavior($slug, $strategy, array $args = []) {
        $behavior = $this->getBehavior($slug, $strategy);
        if ($this->bucket->enabled($behavior)) {
            $this->setBehavior($behavior, $args);
        }
        return $this;
    }

    /**
     * Add a default behavior.
     *
     * Will be used if all other behaviors are not enabled for the user's bucket.
     *
     * @param mixed $strategy
     * @param array $args
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function defaultBehavior($strategy, array $args = []) {
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
     * Execute the feature.
     *
     * @return mixed
     */
    public function execute() {
        $behavior = $this->behavior ?: $this->getBehavior(null);
        $behaviorSlug = $behavior->getSlug();

        $this->metrics && $this->startMetrics($behaviorSlug);
        $result = $behavior->execute($this->args ?: []);
        $this->metrics && $this->stopMetrics($behaviorSlug);

        return $result;
    }

    /**
     * Create and return a new Behavior.
     *
     * If $strategy is not callable, it will be wraped in a closure that returns the strategy.
     *
     * @param string $slug
     * @param mixed $strategy
     * @return \Zumba\Swivel\BehaviorInterface
     */
    public function getBehavior($slug, $strategy = self::DEFAULT_STRATEGY) {
        $this->logger->debug('Swivel - Creating new behavior.', compact('slug'));
        if ($strategy === static::DEFAULT_STRATEGY) {
            $strategy = $slug;
            $slug = static::DEFAULT_SLUG;
        }

        if (!is_callable($strategy)) {
            if (is_array($strategy) && count($strategy) === 2 && is_object($strategy[0]) && is_string($strategy[1])) {
                $closure = function () use ($strategy) {
                    return call_user_func_array($strategy, func_get_args());
                };
                $strategy = $closure->bindTo($strategy[0], $strategy[0]);
            } else {
                $strategy = function() use ($strategy) {
                    return $strategy;
                };
            }
        }
        $slug = $this->slug . Map::DELIMITER . $slug;
        return new Behavior($slug, $strategy, $this->logger);
    }

    /**
     * Waive the default behavior for this feature.
     *
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function noDefault() {
        if ($this->behavior && $this->behavior->getSlug() === static::DEFAULT_SLUG) {
            $exception = new \LogicException('Called `noDefault` after a default behavior was defined.');
            $this->logger->critical('Swivel', compact('exception'));
            throw $exception;
        }
        $this->defaultWaived = true;
        return $this;
    }

    /**
     * Set the behavior and it's args
     *
     * @param \Zumba\Swivel\Behavior $behavior
     * @param array $args
     * @return void
     */
    protected function setBehavior(Behavior $behavior, array $args = []) {
        $slug = $behavior->getSlug();
        $this->logger->debug('Swivel - Setting behavior.', compact('slug', 'args'));
        $this->behavior = $behavior;
        $this->args = $args;
    }

    /**
     * Set a metrics object
     *
     * @param \Zumba\Swivel\MetricsInterface $metrics
     * @return void
     */
    public function setMetrics(MetricsInterface $metrics) {
        $this->metrics = $metrics;
    }

    /**
     * Start collecting metrics about this feature
     *
     * @param string $behaviorSlug
     * @return void
     */
    protected function startMetrics($behaviorSlug) {
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
     * Stop collecting metrics about this feature
     *
     * @param string $behaviorSlug
     * @return void
     */
    protected function stopMetrics($behaviorSlug) {
        $metrics = $this->metrics;
        $metrics->endMemoryProfile('Features', $behaviorSlug);
        $metrics->endTiming('Features', $behaviorSlug);
    }
}
