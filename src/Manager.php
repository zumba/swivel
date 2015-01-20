<?php

namespace Zumba\Swivel;

class Manager implements ManagerInterface {

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
        $this->setBucket($config->getBucket());
    }

    /**
     * Create a new Feature\Builder instance
     *
     * @param string $slug
     * @return \Zumba\Swivel\Feature\Builder
     * @see \Zumba\Swivel\ManagerInterface
     */
    public function forFeature($slug) {
        return new Feature\Builder($slug, $this->bucket);
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
        throw new \BadMethodCallException('Not Yet Implemented');
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
        }
        return $this;
    }
}
