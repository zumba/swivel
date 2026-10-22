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
        [$feature, $behavior] = $this->splitSlug($slug);

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
        [$feature, $behavior] = $this->splitSlug($slug);

        return $this->forFeature($feature)
            ->addValue($behavior, $a)
            ->defaultValue($b)
            ->execute();
    }

    /**
     * Split a slug into its feature and behavior parts.
     *
     * A slug without a delimiter names the feature itself: its behavior part is
     * empty, which the Builder resolves to the feature's own slug. This keeps
     * invoke('Feature', ...) and returnValue('Feature', ...) working as they did
     * before 5.0, when the slug was split with explode()/array_shift().
     *
     * @return array{0: string, 1: string}
     */
    protected function splitSlug(string $slug): array
    {
        return array_pad(explode(Map::DELIMITER, $slug, 2), 2, '');
    }

    /**
     * Set the Swivel Bucket.
     *
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function setBucket(?BucketInterface $bucket = null): ManagerInterface
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
