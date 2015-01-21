<?php
namespace Zumba\Swivel\Feature;

interface BuilderInterface extends \Psr\Log\LoggerAwareInterface {

    /**
     * Register a behavior
     *
     * If $strategy is a callable it's return value will be returned when the behavior is executed;
     * if $strategy is not a callable, it will be directly returned by the behavior when it is
     * executed.
     *
     * @param string $slug
     * @param mixed $strategy
     * @param array $args
     * @return \Zumba\Swivel\Feature\BuilderInterface
     */
    public function addBehavior($slug, $strategy, array $args = []);

    /**
     * A fallback strategy if no added behaviors are active for the bucket.
     *
     * @param mixed $strategy
     * @param array $args
     * @return mixed
     */
    public function defaultBehavior($strategy, array $args = []);

    /**
     * Creates a new Behavior object with an attached strategy.
     *
     * @param string $slug
     * @param mixed $strategy
     * @return \Zumba\Swivel\Behavior
     */
    public function getBehavior($slug, $strategy = null);

    /**
     * Creates a new Feature object with an attached Behavior.
     *
     * @return \Zumba\Swivel\Feature
     */
    public function getFeature();

    /**
     * Indicates that the feature has no default behavior.
     *
     * @return \Zumba\Swivel\Feature\BuilderInterface
     */
    public function noDefault();

    /**
     * Execute the feature and return the result of the behavior.
     *
     * @return mixed
     */
    public function execute();
}
