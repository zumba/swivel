<?php
namespace Zumba\Swivel;

interface ManagerInterface extends \Psr\Log\LoggerAwareInterface {

    /**
     * Create a new Feature\Builder instance
     *
     * @param string $slug
     * @return \Zumba\Swivel\Feature\Builder
     */
    public function forFeature($slug);

    /**
     * Syntactic sugar for creating simple feature toggles (ternary style)
     *
     * @param string $slug
     * @param mixed $a
     * @param mixed $b
     * @return mixed
     */
    public function invoke($slug, $a, $b = null);

    /**
     * Set the Swivel Bucket
     *
     * @param \Zumba\Swivel\BucketInterface $bucket
     * @return \Zumba\Swivel\ManagerInterface
     */
    public function setBucket(BucketInterface $bucket = null);
}
