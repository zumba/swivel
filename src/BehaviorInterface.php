<?php
namespace Zumba\Swivel;

interface BehaviorInterface {

    /**
     * Execute the behavior's callable and return the result
     *
     * @param array $args
     * @return mixed
     */
    public function execute(array $args = []);

    /**
     * Get the behavior's slug
     *
     * @return string
     */
    public function getSlug();
}
