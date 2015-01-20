<?php
namespace Zumba\Swivel\Feature;

use \Zumba\Swivel\Feature;

class Map implements MapInterface {

    /**
     * Map of parsed features
     *
     * @var array
     */
    protected $map;

    /**
     * Zumba\Swivel\Feature\Map
     *
     * Example of $map param:
     * [
     *   'FeatureA' => [1, 2, 3, 4, 5, 6],
     *   'FeatureA.Behavior1' => [1, 2, 3],
     *   'FeatureA.Behavior2' => [4, 5, 6],
     * ]
     *
     * @param array $map
     */
    public function __construct(array $map = []) {
        $this->map = $this->parse($map);
    }

    /**
     * Check if a feature slug is enabled for a particular bucket index
     *
     * @param string $slug
     * @param integer $index Bitmask
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

    /**
     * Reduce an array of integers to a bitmask
     *
     * @param array $list
     * @return integer bitmask
     */
    protected function reduceToBitmask(array $list) {
        return array_reduce($list, function($mask, $index) {
            return $mask | (1 << ($index - 1));
        });
    }

    /**
     * Parse a human readable map into a map of bitmasks
     *
     * @param array $map
     * @return array
     */
    public function parse(array $map) {
        return array_combine(array_keys($map), array_map([$this, 'reduceToBitmask'], $map));
    }
}
