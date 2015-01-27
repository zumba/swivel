<?php

namespace Zumba\Swivel;

class Manager implements ManagerInterface {

    use \Psr\Log\LoggerAwareTrait;

    /**
     * A configured Bucket instance.
     *
     * @var \Zumba\Swivel\BucketInterface
     */
    protected $bucket;

    /**
     * A metrics object
     *
     * @var \Zumba\Swivel\MetricsInterface
     */
    protected $metrics;

    /**
     * Zumba\Swivel\Manager
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config) {
        $this->setLogger($config->getLogger());
        $this->setBucket($config->getBucket());
        $this->setMetrics($config->getMetrics());
        $this->logger->debug('Swivel - Manager created.');
    }

    /**
     * Create a new Builder instance
     *
     * @param string $slug
     * @return \Zumba\Swivel\Builder
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function forFeature($slug) {
        $this->logger->debug('Swivel - Generating builder for feature "' . $slug . '"');
        $builder = new Builder($slug, $this->bucket);
        $builder->setLogger($this->logger);
        $builder->setMetrics($this->metrics);
        return $builder;
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * @param string $slug
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function invoke($slug, $a, $b = null) {
        $parts = explode(Map::DELIMITER, $slug);
        $feature = array_shift($parts);
        return $this->forFeature($feature)
            ->addBehavior(implode(Map::DELIMITER, $parts), $a)
            ->defaultBehavior($b)
            ->execute();
    }

    /**
     * Set the Swivel Bucket
     *
     * @param \Zumba\Swivel\BucketInterface $bucket
     * @return \Zumba\Swivel\ManagerInterface
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function setBucket(BucketInterface $bucket = null) {
        if ($bucket) {
            $this->bucket = $bucket;
            $this->logger->debug('Swivel - User bucket set.', compact('bucket'));
        }
        return $this;
    }

    /**
     * Set a metrics object
     *
     * @param MetricsInterface $metrics
     * @return void
     */
    protected function setMetrics(MetricsInterface $metrics) {
        $this->metrics = $metrics;
    }
}
