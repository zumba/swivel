<?php

namespace Zumba\Swivel;

interface MapInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Merge this map with another map and return a new MapInterface.
     *
     * Values in $map will be added to values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     *
     * @param MapInterface $map
     *
     * @return MapInterface
     */
    public function add(MapInterface $map);

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that differ between the two maps. If a feature
     * with the same key has different buckets, the buckets from the passed-in $map will be in the
     * new object.  If this map has a logger, it will be passed to the new map.
     *
     * @param MapInterface $map
     *
     * @return MapInterface
     */
    public function diff(MapInterface $map);

    /**
     * Check if a feature slug is enabled for a particular bucket index.
     *
     * @param string $slug
     * @param binary $index
     *
     * @return bool
     */
    public function enabled($slug, $index);

    /**
     * Get the internal map array used by this map object.
     *
     * @return array
     */
    public function getMapData();

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that match between the two maps. If this map
     * has a logger, it will be passed to the new map.
     *
     * @param MapInterface $map
     *
     * @return MapInterface
     */
    public function intersect(MapInterface $map);

    /**
     * Merge this map with another map and return a new MapInterface.
     *
     * Values in $map will overwrite values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     *
     * @param MapInterface $map
     *
     * @return MapInterface
     */
    public function merge(MapInterface $map);

    /**
     * Parse a human readable map into a map of bitmasks.
     *
     * @param array $map
     *
     * @return array
     */
    public function parse(array $map);
}
