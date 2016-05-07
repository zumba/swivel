<?php

namespace Zumba\Swivel;

use Zumba\Swivel\MapInterface;
use Psr\Log\LoggerInterface;
use Zumba\Swivel\Logging\NullLogger;

class Bucket implements BucketInterface
{
    use \Psr\Log\LoggerAwareTrait;

    /**
     * Ordinal Bitmasks.
     */
    const FIRST = 0b0000000001;
    const SECOND = 0b0000000010;
    const THIRD = 0b0000000100;
    const FOURTH = 0b0000001000;
    const FIFTH = 0b0000010000;
    const SIXTH = 0b0000100000;
    const SEVENTH = 0b0001000000;
    const EIGHTH = 0b0010000000;
    const NINTH = 0b0100000000;
    const TENTH = 0b1000000000;
    const ALL = 0b1111111111;

    /**
     * The feature map.
     *
     * @var Zumba\Swivel\MapInterface
     */
    protected $featureMap;

    /**
     * The user's index.
     *
     * @var int Binary
     */
    protected $index;

    /**
     * Zumba\Swivel\Bucket.
     *
     * @param \Zumba\Swivel\MapInterface $featureMap
     * @param int|null                   $index
     * @param \Psr\Log\LoggerInterface   $logger
     */
    public function __construct(MapInterface $featureMap, $index = null, LoggerInterface $logger = null)
    {
        $this->setLogger($logger ?: new NullLogger());
        $this->featureMap = $featureMap;
        $this->index = $index === null ? $this->randomIndex() : $index;
    }

    /**
     * Check if a behavior is enabled for a particular context/bucket combination.
     *
     * @param \Zumba\Swivel\BehaviorInterface $behavior
     *
     * @return bool
     *
     * @see \Zumba\Swivel\BucketInterface
     */
    public function enabled(BehaviorInterface $behavior)
    {
        return $this->featureMap->enabled($behavior->getSlug(), $this->index);
    }

    /**
     * Get the bucket index.
     *
     * Useful for metrics.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Get a random index.
     *
     * @return int
     */
    protected function randomIndex()
    {
        return mt_rand(1, 10);
    }
}
