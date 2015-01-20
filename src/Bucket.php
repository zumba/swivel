<?php
namespace Zumba\Swivel;

use \Zumba\Swivel\MapInterface,
    \Psr\Log\LoggerInterface,
    \Psr\Log\NullLogger;

class Bucket implements BucketInterface {

    use \Psr\Log\LoggerAwareTrait;

    /**
     * Ordnial Bitmasks
     */
    const FIRST   = 0b0000000001;
    const SECOND  = 0b0000000010;
    const THIRD   = 0b0000000100;
    const FOURTH  = 0b0000001000;
    const FIFTH   = 0b0000010000;
    const SIXTH   = 0b0000100000;
    const SEVENTH = 0b0001000000;
    const EIGHTH  = 0b0010000000;
    const NINTH   = 0b0100000000;
    const TENTH   = 0b1000000000;
    const ALL     = 0b1111111111;

    /**
     * The feature map
     *
     * @var Zumba\Swivel\MapInterface
     */
    protected $featureMap;

    /**
     * The user's index
     *
     * @var integer Binary
     */
    protected $index;

    /**
     * Keys used by Zumba\Swivel\Bucket::randomIndex to select a random constant
     *
     * @var array
     */
    private $keys = [
        'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'FIFTH',
        'SIXTH', 'SEVENTH', 'EIGHTH', 'NINTH', 'TENTH'
    ];

    /**
     * Zumba\Swivel\Bucket
     *
     * @param \Zumba\Swivel\MapInterface $featureMap
     * @param binary|null $index
     */
    public function __construct(MapInterface $featureMap, $index = null, LoggerInterface $logger = null) {
        $this->setLogger($logger ?: new NullLogger());
        $this->featureMap = $featureMap;
        $this->index = $index === null ? $this->randomIndex() : $index;
    }

    /**
     * Check if a behavior is enabled for a particular context/bucket combination
     *
     * @param \Zumba\Swivel\BehaviorInterface $behavior
     * @return boolean
     * @see \Zumba\Swivel\BucketInterface
     */
    public function enabled(BehaviorInterface $behavior) {
        return $this->featureMap->enabled($behavior->getSlug(), $this->index);
    }

    /**
     * Get a random binary index
     *
     * @return binary
     */
    protected function randomIndex() {
        $key = $this->keys[$this->randomNumber()];
        $this->logger->info('Swivel - Generated random bucket.', compact('key'));
        return constant("static::$key");
    }

    /**
     * Return a random integer between 0 and 9
     *
     * @return integer
     */
    private function randomNumber() {
        return mt_rand(0, 9);
    }
}
