<?php

namespace Zumba\Swivel;

interface ManagerInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Create a new Builder instance.
     */
    public function forFeature(string $slug): BuilderInterface;

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style).
     */
    public function invoke(string $slug, mixed $a, mixed $b = null);

    /**
     * Set the Swivel Bucket.
     */
    public function setBucket(BucketInterface $bucket = null): ManagerInterface;
}
