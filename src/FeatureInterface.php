<?php
namespace Zumba\Swivel;

interface FeatureInterface {

    /**
     * Attach a beavior to the feature.
     *
     * @param BehaviorInterface $behavior
     * @param array $args
     * @return \Zumba\Swivel\FeatureInterface
     */
    public function attach(BehaviorInterface $behavior, array $args = []);

    /**
     * Execute the attached behavior and return the result.
     *
     * @return mixed
     */
    public function execute();
}
