<?php

namespace Zumba\Swivel;

use Psr\Log\LoggerInterface;

interface ConfigInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Get a configured Bucket instance.
     */
    public function getBucket(): BucketInterface;

    /**
     * Get the PSR3 logger.
     */
    public function getLogger(): LoggerInterface;

    /**
     * Get the Metrics object.
     */
    public function getMetrics() : ?MetricsInterface;

    /**
     * Set the bucket index for the user.
     */
    public function setBucketIndex(int $index): void;

    /**
     * Set the Metrics object.
     */
    public function setMetrics(MetricsInterface $metrics): void;
}
