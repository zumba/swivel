<?php

namespace Zumba\Swivel;

interface BucketInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Check if a behavior is enabled for a particular context/bucket combination.
     *
     * @param BehaviorInterface $behavior
     *
     * @return bool
     */
    public function enabled(BehaviorInterface $behavior);

    /**
     * Get the bucket index.
     *
     * Useful for metrics.
     *
     * @return int
     */
    public function getIndex();

    /**
     * Sets the callback
     *
     * @param callable $callback
     */
    public function setMissingSlugCallback(callable $callback);
}
