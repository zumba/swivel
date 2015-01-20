<?php
namespace Zumba\Swivel;

interface BucketInterface extends \Psr\Log\LoggerAwareInterface {

    /**
     * Check if a behavior is enabled for a particular context/bucket combination
     *
     * @param BehaviorInterface $behavior
     * @return boolean
     */
    public function enabled(BehaviorInterface $behavior);
}
