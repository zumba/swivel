<?php
namespace Zumba\Swivel;

use \Psr\Log\LoggerInterface,
    \Zumba\Swivel\Logging\NullLogger;

class Map implements MapInterface {

    use \Psr\Log\LoggerAwareTrait;

    const DELIMITER = '.';

    /**
     * Map of parsed features
     *
     * @var array
     */
    protected $map;

    /**
     * Zumba\Swivel\Map
     *
     * Example of $map param:
     * [
     *   'FeatureA' => [1, 2, 3, 4, 5, 6],
     *   'FeatureA.Behavior1' => [1, 2, 3],
     *   'FeatureA.Behavior2' => [4, 5, 6],
     * ]
     *
     * @param array $map
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(array $map = [], LoggerInterface $logger = null) {
        $this->setLogger($logger ?: new NullLogger());
        $this->map = $this->parse($map);
    }

    /**
     * Merge this map with another map and return a new MapInterface
     *
     * Values in $map will be added to values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     *
     * @param MapInterface $map
     * @return MapInterface
     */
    public function add(MapInterface $map) {
        $combine = function($data, $map) {
            foreach ($map as $key => $mask) {
                $data[$key] = empty($data[$key]) ? $mask : ($data[$key] | $mask);
            }
            return $data;
        };

        $maps = array_slice(func_get_args(), 1);
        $data = array_reduce($maps, $combine, $combine($this->map, $map->getMapData()));
        return new Map($data, $this->logger);
    }

    /**
     * SetState 
     *
     * Support reloading class via var_export definition.
     * 
     * @param array $mapData Array of logger data needed to reconsturct logger
     * @return string        Implementaiton of logger class to be passed to the Map class
     */
    public static function __set_state($mapData) {
        $map = new static($mapData['map'], $mapData['logger']);
        return $map;
    }

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that differ between the two maps. If a feature
     * with the same key has different buckets, the buckets from the passed-in $map will be in the
     * new object.
     *
     * If this map has a logger, it will be passed to the new map.
     *
     * @param MapInterface $map
     * @return MapInterface
     */
    public function diff(MapInterface $map) {
        $otherMapData = $map->getMapData();
        $data = array_merge(
            array_diff_assoc($this->map, $otherMapData),
            array_diff_assoc($otherMapData, $this->map)
        );
        return new Map($data);
    }

    /**
     * Check if a feature slug is enabled for a particular bucket index
     *
     * @param string $slug
     * @param integer $index
     * @return boolean
     * @see \Zumba\Swivel\MapInterface
     */
    public function enabled($slug, $index) {
        $map = $this->map;
        $key = '';
        $index = 1 << ($index - 1);
        foreach (explode(static::DELIMITER, $slug) as $child) {
            $key = empty($key) ? $child : $key . static::DELIMITER . $child;
            if (!isset($map[$key]) || !($map[$key] & $index)) {
                $this->logger->debug('Swivel - "' . $slug . '" is not enabled for bucket ' . $index);
                return false;
            }
        }
        $this->logger->debug('Swivel - "' . $slug . '" is enabled for bucket ' . $index);
        return true;
    }

    /**
     * Get the internal map array used by this map object.
     *
     * @return array
     */
    public function getMapData() {
        return $this->map;
    }

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that match between the two maps. If this map
     * has a logger, it will be passed to the new map.
     *
     * @param MapInterface $map
     * @return MapInterface
     */
    public function intersect(MapInterface $map) {
        return new Map(array_intersect_assoc($this->map, $map->getMapData()), $this->logger);
    }

    /**
     * Merge this map with another map and return a new MapInterface
     *
     * Values in $map will overwrite values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     *
     * @param MapInterface $map
     * @return MapInterface
     */
    public function merge(MapInterface $map) {
        $maps = array_slice(func_get_args(), 1);
        $data = array_reduce($maps, 'array_merge', array_merge($this->map, $map->getMapData()));
        return new Map($data, $this->logger);
    }

    /**
     * Reduce an array of integers to a bitmask if $list is an array.
     *
     * Otherwise, this method will just return $list.
     *
     * @param mixed $list
     * @return integer bitmask
     */
    protected function reduceToBitmask($list) {
        $this->logger->debug('Swivel - reducing to bitmask.', compact('list'));
        return !is_array($list) ? $list : array_reduce($list, function($mask, $index) {
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
