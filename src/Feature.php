<?php
namespace Zumba\Swivel;

class Feature implements FeatureInterface {

    const DELIMITER = '.';

    /**
     * Bitmask for feature completely off.
     */
    const OFF = 0;

    /**
     * Behavior Arguments
     *
     * @var array
     */
    protected $args = [];

    /**
     * Behavior
     *
     * @var \Zumba\Swivel\BehaviorInterface
     */
    protected $behavior;

    /**
     * Attach a beavior to the feature.
     *
     * @param BehaviorInterface $behavior
     * @param array $args
     * @return \Zumba\Swivel\FeatureInterface
     * @see \Zumba\Swivel\FeatureInterface
     */
    public function attach(BehaviorInterface $behavior, array $args = []) {
        $this->behavior = $behavior;
        $this->args = $args;
        return $this;
    }

    /**
     * Execute the attached behavior and return the result.
     *
     * @return mixed
     * @see \Zumba\Swivel\FeatureInterface
     */
    public function execute() {
        return $this->behavior->execute($this->args);
    }
}
