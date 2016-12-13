<?php
namespace Zumba\Swivel;

use \Psr\Log\LoggerInterface,
    \Zumba\Swivel\Logging\NullLogger;

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
     * Metrics object
     *
     * @var \Zumba\Swivel\MetricsInterface
     */
    protected $metrics;

    /**
     * Callback to handle a missing slug from Map
     *
     * @var callable
     */
    protected $callback;

    /**
     * Zumba\Swivel\Config.
     *
     * @param mixed $map
     * @param integer|null $index
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param callable|null                 $callback
     */
    public function __construct($map = [], $index = null, LoggerInterface $logger = null, $callback = null)
    {
        $this->setLogger($logger ?: $this->getLogger());
        $this->setMap($map);
        $this->index = $index;
        $this->callback = $callback;
    }

    /**
     * Get a configured Bucket instance
     *
     * @return \Zumba\Swivel\Bucket
     */
    public function getBucket()
    {
        return new Bucket($this->map, $this->index, $this->getLogger(), $this->callback);
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
     * Get the Metrics object
     *
     * @return \Zumba\Swivel\MetricsInterface
     */
    public function getMetrics() {
        return $this->metrics;
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

    /**
     * Set the Metrics object
     *
     * @param MetricsInterface $metrics
     * @return void
     */
    public function setMetrics(MetricsInterface $metrics) {
        $this->metrics = $metrics;
    }
}
