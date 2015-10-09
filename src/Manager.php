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

        if ($metrics = $config->getMetrics()) {
            $this->setMetrics($metrics);
        }

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

        $this->metrics && $builder->setMetrics($this->metrics);

        return $builder;
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * Uses Builder::addBehavior
     *
     * @param string $slug
     * @param callable $a
     * @param callable $b
     * @return mixed
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function invoke($slug, $a, $b = null) {
        $parts = explode(Map::DELIMITER, $slug);
        $feature = array_shift($parts);
        return $this->forFeature($feature)
            ->addBehavior(implode(Map::DELIMITER, $parts), $a)
            ->defaultBehavior($b ? $b : function () use ($b) { return $b; })
            ->execute();
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * Uses Builder::addValue
     *
     * @param string $slug
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function returnValue($slug, $a, $b = null) {
        $parts = explode(Map::DELIMITER, $slug);
        $feature = array_shift($parts);
        return $this->forFeature($feature)
            ->addValue(implode(Map::DELIMITER, $parts), $a)
            ->defaultValue($b)
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
