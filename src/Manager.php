<?php

namespace Zumba\Swivel;

class Manager implements ManagerInterface
{
    use \Psr\Log\LoggerAwareTrait;

    /**
     * A configured Bucket instance.
     */
    protected ?BucketInterface $bucket = null;

    /**
     * A metrics object.
     */
    protected ?MetricsInterface $metrics = null;

    /**
     * Zumba\Swivel\Manager.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->setLogger($config->getLogger());
        $this->setBucket($config->getBucket());

        if ($metrics = $config->getMetrics()) {
            $this->setMetrics($metrics);
        }

        $this->logger->debug('Swivel - Manager created.');
    }

    /**
     * Create a new Builder instance.
     *
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function forFeature(string $slug): BuilderInterface
    {
        $this->logger->debug('Swivel - Generating builder for feature "'.$slug.'"');
        $builder = new Builder($slug, $this->bucket);
        $builder->setLogger($this->logger);

        $this->metrics && $builder->setMetrics($this->metrics);

        return $builder;
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style).
     *
     * Uses Builder::addBehavior
     *
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function invoke(string $slug, mixed $a, mixed $b = null): mixed
    {
        list($feature, $behavior) = explode(Map::DELIMITER, $slug, 2);

        return $this->forFeature($feature)
            ->addBehavior($behavior, $a)
            ->defaultBehavior($b ?: fn() => $b)
            ->execute();
    }

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style).
     *
     * Uses Builder::addValue
     *
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function returnValue(string $slug, mixed $a, mixed $b = null): mixed
    {
        list($feature, $behavior) = explode(Map::DELIMITER, $slug, 2);

        return $this->forFeature($feature)
            ->addValue($behavior, $a)
            ->defaultValue($b)
            ->execute();
    }

    /**
     * Set the Swivel Bucket.
     *
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function setBucket(BucketInterface $bucket = null): ManagerInterface
    {
        if ($bucket) {
            $this->bucket = $bucket;
            $this->logger->debug('Swivel - User bucket set.', compact('bucket'));
        }

        return $this;
    }

    /**
     * Set a metrics object.
     *
     * @param MetricsInterface $metrics
     */
    protected function setMetrics(MetricsInterface $metrics): void
    {
        $this->metrics = $metrics;
    }
}
