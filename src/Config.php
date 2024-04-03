<?php

namespace Zumba\Swivel;

use Psr\Log\LoggerInterface;
use Zumba\Swivel\Logging\NullLogger;

class Config implements ConfigInterface
{
    use \Psr\Log\LoggerAwareTrait;

    /**
     * Index of the user's bucket.
     */
    protected ?int $index;

    /**
     * Map of features.
     *
     * @var \Zumba\Swivel\Map|\Zumba\Swivel\DriverInterface
     */
    protected $map = null;

    /**
     * Metrics object.
     *
     * @var \Zumba\Swivel\MetricsInterface
     */
    protected ?MetricsInterface $metrics = null;

    /**
     * Callback to handle a missing slug from Map
     *
     * @var callable
     */
    protected $callback;

    /**
     * Zumba\Swivel\Config.
     */
    public function __construct(
        mixed $map = [],
        ?int $index = null,
        LoggerInterface $logger = null,
        ?callable $callback = null
    ) {
        $this->setLogger($logger ?: $this->getLogger());
        $this->setMap($map);
        $this->index = $index;
        $this->callback = $callback;
    }

    /**
     * Get a configured Bucket instance.
     */
    public function getBucket(): BucketInterface
    {
        return new Bucket($this->map, $this->index, $this->getLogger(), $this->callback);
    }

    /**
     * Get the PSR3 logger.
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger ?: new NullLogger();
    }

    /**
     * Get the Metrics object.
     */
    public function getMetrics(): ?MetricsInterface
    {
        return $this->metrics;
    }

    /**
     * Set the bucket index for the user.
     */
    public function setBucketIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * Set the Zumba\Swivel\Map object.
     */
    protected function setMap(mixed $map): void
    {
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
     * Set the Metrics object.
     *
     * @param MetricsInterface $metrics
     */
    public function setMetrics(MetricsInterface $metrics): void
    {
        $this->metrics = $metrics;
    }
}
