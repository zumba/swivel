<?php
namespace Zumba\Swivel;

use \Psr\Log\LoggerInterface,
    \Psr\Log\NullLogger;

class Config implements ConfigInterface {

    use \Psr\Log\LoggerAwareTrait;

    /**
     * Index of the user's bucket
     *
     * @var integer
     */
    protected $index;

    /**
     * Map of features
     *
     * @var \Zumba\Swivel\Map
     */
    protected $map;

    /**
     * Zumba\Swivel\Config
     *
     * @param mixed $map
     * @param integer|null $index
     * @param LoggerInterface|null $logger
     */
    public function __construct($map = [], $index = null, LoggerInterface $logger = null) {
        $this->setLogger($logger ?: $this->getLogger());
        $this->setMap($map);
        $this->index = $index;
    }

    /**
     * Get a configured Bucket instance
     *
     * @return \Zumba\Swivel\Bucket
     */
    public function getBucket() {
        return new Bucket($this->map, $this->index, $this->getLogger());
    }

    /**
     * Get the PSR3 logger
     *
     * @return \Psr\Log\LoggerInterface|null
     */
    public function getLogger() {
        return $this->logger ?: new NullLogger();
    }

    /**
     * Set the bucket index for the user
     *
     * @param integer $index
     * @return void
     */
    public function setBucketIndex($index) {
        $this->index = $index;
    }

    /**
     * Set the Zumba\Swivel\Map object
     *
     * @param mixed $map
     * @return void
     */
    protected function setMap($map) {
        $logger = $this->getLogger();
        if (is_array($map)) {
            $map = new Map($map, $logger);
        } elseif ($map instanceof DriverInterface) {
            $map = $map->getMap();
            $map->setLogger($logger);
        } elseif ($map instanceof MapInterface) {
            $map->setLogger($logger);
        } else {
            throw new \LogicException('Invalid map passed to Zumba\Swivel\Config');
        }
        $this->map = $map;
    }
}
