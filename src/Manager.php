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
     * Zumba\Swivel\Manager
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config) {
        $this->setLogger($config->getLogger());
        $this->setBucket($config->getBucket());
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
        return new Builder($slug, $this->bucket, $this->logger);
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
        $exception = new \BadMethodCallException('Invoke Not Yet Implemented');
        $this->logger->critical('Swivel', compact('exception'));
        throw $exception;
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
}
