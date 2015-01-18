<?php
namespace Zumba\Swivel;

interface BucketInterface {

    /**
     * Check if a behavior is enabled for a particular context/bucket combination
     *
     * @param BehaviorInterface $behavior
     * @return boolean
     */
    public function enabled(BehaviorInterface $behavior);
}
