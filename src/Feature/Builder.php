<?php
namespace Zumba\Swivel\Feature;

use Zumba\Swivel\BucketInterface,
    Zumba\Swivel\Behavior;

class Builder implements BuilderInterface {

    const DEFAULT_SLUG = '__swivel_default';

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
     * Parent Feature slug
     *
     * @var string
     */
    protected $slug;

    /**
     * Zumba\Swivel\Feature\Builder
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
     * @return \Zumba\Swivel\Feature\BuilderInterface
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
     * @return \Zumba\Swivel\Feature\BuilderInterface
     */
    public function defaultBehavior($strategy, array $args = []) {
        if ($this->defaultWaived) {
            throw new \LogicException('Defined a default behavior after `noDefault` was called.');
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
        return $this->getFeature()->execute();
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
    public function getBehavior($slug, $strategy = null) {
        if (empty($strategy)) {
            $strategy = $slug;
            $slug = Behavior::DEFAULT_SLUG;
        }

        if (!is_callable($strategy)) {
            $strategy = function() use ($strategy) {
                return $strategy;
            };
        }
        $slug = $this->slug . \Zumba\Swivel\Feature::DELIMITER . $slug;
        return new Behavior($slug, $strategy);
    }

    /**
     * Create and return a new Feature with an attached behavior
     *
     * @return \Zumba\Swivel\FeatureInterface
     */
    public function getFeature() {
        $feature = new \Zumba\Swivel\Feature();
        return $feature->attach($this->behavior ?: function() {}, $this->args);
    }

    /**
     * Waive the default behavior for this feature.
     *
     * @return \Zumba\Swivel\Feature\BuilderInterface
     */
    public function noDefault() {
        if ($this->behavior && $this->behavior->getSlug() === static::DEFAULT_SLUG) {
            throw new \LogicException('Called `noDefault` after a default behavior was defined.');
        }
        $this->defaultWaived = true;
        return $this;
    }

    /**
     * Set the behavior and it's args
     *
     * @param \Zumba\Feature\Behavior $behavior
     * @param array $args
     * @return void
     */
    protected function setBehavior(Behavior $behavior, array $args = []) {
        $this->behavior = $behavior;
        $this->args = $args;
    }
}
