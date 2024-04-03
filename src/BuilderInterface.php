<?php

namespace Zumba\Swivel;

interface BuilderInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Register a behavior.
     *
     * If $strategy is a callable it's return value will be returned when the behavior is executed;
     * if $strategy is not a callable, it will be directly returned by the behavior when it is
     * executed.
     */
    public function addBehavior(string $slug, mixed $strategy, array $args = []): BuilderInterface;

    /**
     * A fallback strategy if no added behaviors are active for the bucket.
     */
    public function defaultBehavior(mixed $strategy, array $args = []): mixed;

    /**
     * Add a value to be returned when the builder is executed.
     *
     * Value will only be returned if it is enabled for the user's bucket.
     */
    public function addValue(string $slug, mixed $value): BuilderInterface;

    /**
     * Add a default value.
     *
     * Will be used if all other behaviors and values are not enabled for the user's bucket.
     */
    public function defaultValue(mixed $value): BuilderInterface;

    /**
     * Creates a new Behavior object with an attached strategy.
     */
    public function getBehavior(string|callable $slug, mixed $strategy = null): BehaviorInterface;

    /**
     * Indicates that the feature has no default behavior.
     */
    public function noDefault(): BuilderInterface;

    /**
     * Execute the feature and return the result of the behavior.
     */
    public function execute(): mixed;

    /**
     * Set a metrics object.
     */
    public function setMetrics(MetricsInterface $metrics): void;
}
