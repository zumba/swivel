<?php
namespace Zumba\Swivel\Feature;

use \Zumba\Swivel\Feature,
    \Psr\Log\LoggerInterface,
    \Psr\Log\NullLogger;

class Map implements MapInterface {

    use \Psr\Log\LoggerAwareTrait;

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
    public function __construct(array $map = [], LoggerInterface $logger = null) {
        $this->setLogger($logger ?: new NullLogger());
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
                $this->logger->debug('Swivel - "' . $slug . '" is not enabled for bucket ' . $index);
                return false;
            }
        }
        $this->logger->debug('Swivel - "' . $slug . '" is enabled for bucket ' . $index);
        return true;
    }

    /**
     * Reduce an array of integers to a bitmask
     *
     * @param array $list
     * @return integer bitmask
     */
    protected function reduceToBitmask(array $list) {
        $this->logger->debug('Swivel - reducing to bitmask.', compact('list'));
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
        $this->logger->info('Swivel - Parsing feature map.', compact('map'));
        return array_combine(array_keys($map), array_map([$this, 'reduceToBitmask'], $map));
    }
}
