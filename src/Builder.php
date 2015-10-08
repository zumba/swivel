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
     * Add a value to be returned when the builder is executed.
     *
     * Value will only be returned if it is enabled for the user's bucket.
     *
     * @param string $slug
     * @param mixed $value
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function addValue($slug, $value) {
        $behavior = $this->getBehavior($slug, function() use ($value) {
            return $value;
        });
        if ($this->bucket->enabled($behavior)) {
            $this->setBehavior($behavior);
        }
        return $this;
    }

    /**
     * Add a default behavior.
     *
     * Will be used if all other behaviors and values are not enabled for the user's bucket.
     *
     * @param callable $strategy
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
     * Add a default value.
     *
     * Will be used if all other behaviors and values are not enabled for the user's bucket.
     *
     * @param mixed $value
     * @return \Zumba\Swivel\BuilderInterface
     */
    public function defaultValue($value) {
        if ($this->defaultWaived) {
            $exception = new \LogicException('Defined a default alue after `noDefault` was called.');
            $this->logger->critical('Swivel', compact('exception'));
            throw $exception;
        }
        if (!$this->behavior) {
            $callable = function() use ($value) { return $value; };
            $this->setBehavior($this->getBehavior($callable));
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
     * The $strategy parameter must be a valid callable.
     *
     * @param string $slug
     * @param callable $strategy
     * @return \Zumba\Swivel\BehaviorInterface
     */
    public function getBehavior($slug, $strategy = self::DEFAULT_STRATEGY) {
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
            $closure = function() use ($strategy) {
                return call_user_func_array($strategy, func_get_args());
            };
            $strategy = $closure->bindTo(null, $strategy[0]);
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
