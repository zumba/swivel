<?php
namespace Zumba\Swivel;

class Behavior implements BehaviorInterface {

    /**
     * Fully qualified feature slug
     *
     * E.g. "Feature.New.VersionA"
     *
     * @var string
     */
    protected $slug;

    /**
     * The strategy to be executed
     *
     * @var callable
     */
    protected $strategy;

    /**
     * Zumba\Swivel\Behavior
     *
     * @param string $slug
     * @param callable $strategy
     */
    public function __construct($slug, callable $strategy) {
        $this->slug = $slug;
        $this->strategy = $strategy;
    }

    /**
     * Execute the behavior's callable and return the result
     *
     * @param array $args
     * @return mixed
     * @see \Zumba\Swivel\BehaviorInterface
     */
    public function execute(array $args = []) {
        return call_user_func_array($this->strategy, $args);
    }

    /**
     * Get the behavior's slug
     *
     * @return string
     * @see \Zumba\Swivel\BehaviorInterface
     */
    public function getSlug() {
        return $this->slug;
    }
}
