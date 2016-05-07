<?php

namespace Zumba\Swivel;

interface ConfigInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Get a configured Bucket instance.
     *
     * @return \Zumba\Swivel\Bucket
     */
    public function getBucket();

    /**
     * Get the PSR3 logger.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger();

    /**
     * Get the Metrics object.
     *
     * @return \Zumba\Swivel\MetricsInterface
     */
    public function getMetrics();

    /**
     * Set the bucket index for the user.
     *
     * @param int $index
     */
    public function setBucketIndex($index);

    /**
     * Set the Metrics object.
     *
     * @param MetricsInterface $metrics
     */
    public function setMetrics(MetricsInterface $metrics);
}
