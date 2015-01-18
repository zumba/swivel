<?php
namespace Zumba\Swivel\Feature;

use \Zumba\Swivel\Feature;

class Map implements MapInterface {

    /**
     * Map of features
     *
     * Example:
     * [
     *   'FeatureA' => [1, 2, 3, 4, 5, 6],
     *   'FeatureA.Behavior1' => [1, 2, 3],
     *   'FeatureA.Behavior2' => [4, 5, 6],
     * ]
     *
     * @var array
     */
    protected $map;

    /**
     * Zumba\Swivel\Feature\Map
     *
     * @param array $map
     */
    public function __construct(array $map = []) {
        $this->map = $map;
    }

    /**
     * Check if a feature slug is enabled for a particular bucket index
     *
     * @param string $slug
     * @param binary $index
     * @return boolean
     * @see \Zumba\Swivel\Feature\MapInterface
     */
    public function enabled($slug, $index) {
        $map = $this->map;
        $key = '';
        foreach (explode(Feature::DELIMITER, $slug) as $child) {
            $key = empty($key) ? $child : $key . Feature::DELIMITER . $child;
            if (!isset($map[$key]) || !($map[$key] & $index)) {
                return false;
            }
        }
        return true;
    }
}
