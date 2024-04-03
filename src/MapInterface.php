<?php

namespace Zumba\Swivel;

interface MapInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Merge this map with another map and return a new MapInterface.
     *
     * Values in $map will be added to values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     */
    public function add(MapInterface $map): MapInterface;

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that differ between the two maps. If a feature
     * with the same key has different buckets, the buckets from the passed-in $map will be in the
     * new object.  If this map has a logger, it will be passed to the new map.
     */
    public function diff(MapInterface $map): MapInterface;

    /**
     * Check if a feature slug is enabled for a particular bucket index.
     */
    public function enabled(string $slug, int $index): bool;

    /**
     * Get the internal map array used by this map object.
     */
    public function getMapData(): array;

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that match between the two maps. If this map
     * has a logger, it will be passed to the new map.
     */
    public function intersect(MapInterface $map): MapInterface;

    /**
     * Merge this map with another map and return a new MapInterface.
     *
     * Values in $map will overwrite values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     */
    public function merge(MapInterface $map): MapInterface;

    /**
     * Parse a human readable map into a map of bitmasks.
     */
    public function parse(array $map): array;
}
