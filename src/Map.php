<?php

namespace Zumba\Swivel;

use Psr\Log\LoggerInterface;
use Zumba\Swivel\Logging\NullLogger;

class Map implements MapInterface
{
    use \Psr\Log\LoggerAwareTrait;

    const DELIMITER = '.';

    /**
     * Map of parsed features.
     *
     * @var array
     */
    protected array $map;

    /**
     * Zumba\Swivel\Map.
     *
     * Example of $map param:
     * [
     *   'FeatureA' => [1, 2, 3, 4, 5, 6],
     *   'FeatureA.Behavior1' => [1, 2, 3],
     *   'FeatureA.Behavior2' => [4, 5, 6],
     * ]
     *
     * @param array                    $map
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(array $map = [], LoggerInterface $logger = null)
    {
        $this->setLogger($logger ?: new NullLogger());
        $this->map = $this->parse($map);
    }

    /**
     * For serialization, removing logger since we can't guarantee a safe serialization and unserialization.
     */
    public function __sleep(): array
    {
        return ['map'];
    }

    /**
     * Ensure a null logger is in place post unserilization so there's no issues.
     */
    public function __wakeup(): void
    {
        $this->setLogger(new NullLogger());
    }

    /**
     * Merge this map with another map and return a new MapInterface.
     *
     * Values in $map will be added to values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     */
    public function add(MapInterface $map): MapInterface
    {
        $combine = function ($data, $map) {
            foreach ($map as $key => $mask) {
                $data[$key] = empty($data[$key]) ? $mask : ($data[$key] | $mask);
            }

            return $data;
        };

        $maps = array_slice(func_get_args(), 1);
        $data = array_reduce($maps, $combine, $combine($this->map, $map->getMapData()));

        return new self($data, $this->logger);
    }

    /**
     * SetState.
     *
     * Support reloading class via var_export definition.
     */
    public static function __set_state($mapData): object
    {
        return new static($mapData['map'], $mapData['logger']);
    }

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that differ between the two maps. If a feature
     * with the same key has different buckets, the buckets from the passed-in $map will be in the
     * new object.
     *
     * If this map has a logger, it will be passed to the new map.
     */
    public function diff(MapInterface $map): MapInterface
    {
        $otherMapData = $map->getMapData();
        $data = array_merge(
            array_diff_assoc($this->map, $otherMapData),
            array_diff_assoc($otherMapData, $this->map)
        );

        return new self($data);
    }

    /**
     * Check if a feature slug is enabled for a particular bucket index.
     *
     * @see \Zumba\Swivel\MapInterface
     */
    public function enabled(string $slug, int $index): bool
    {
        $map = $this->map;
        $key = '';
        $index = (int)$index == 0 ? 0 : 1 << ($index - 1);
        foreach (explode(static::DELIMITER, $slug) as $child) {
            $key = empty($key) ? $child : $key.static::DELIMITER.$child;

            $isMissing = !$this->slugExists($key);
            $isDisabled = $isMissing ?: !((int)$map[$key] & $index);

            if ($isMissing || $isDisabled) {
                $this->logger->debug('Swivel - "'.$slug.'" is not enabled for bucket '.$index);

                return false;
            }
        }
        $this->logger->debug('Swivel - "'.$slug.'" is enabled for bucket '.$index);

        return true;
    }

    /**
     * Check if a feature slug exists in the Map.
     */
    public function slugExists(string $slug): bool
    {
        return isset($this->map[$slug]);
    }

    /**
     * Get the internal map array used by this map object.
     */
    public function getMapData(): array
    {
        return $this->map;
    }

    /**
     * Compare $map to this instance and return a new MapInterface.
     *
     * Returned object will contain only the elements that match between the two maps. If this map
     * has a logger, it will be passed to the new map.
     */
    public function intersect(MapInterface $map): MapInterface
    {
        return new self(array_intersect_assoc($this->map, $map->getMapData()), $this->logger);
    }

    /**
     * Merge this map with another map and return a new MapInterface.
     *
     * Values in $map will overwrite values in this instance.  Any number of additional maps may
     * be passed to this method, i.e. $map->merge($map2, $map3, $map4, ...);
     */
    public function merge(MapInterface $map): MapInterface
    {
        $maps = array_slice(func_get_args(), 1);
        $data = array_reduce($maps, 'array_merge', array_merge($this->map, $map->getMapData()));

        return new self($data, $this->logger);
    }

    /**
     * Reduce an array of integers to a bitmask if $list is an array.
     *
     * Otherwise, this method will just return $list.
     */
    protected function reduceToBitmask(mixed $list): int
    {
        $this->logger->debug('Swivel - reducing to bitmask.', compact('list'));

        if (!is_array($list)) {
            return $list;
        }

        $mask = 0;
        foreach ($list as $value) {
            if ($value > 0) {
                $mask |= (1 << ((int)$value - 1));
            }
        }
        return $mask;
    }

    /**
     * Parse a human readable map into a map of bitmasks.
     */
    public function parse(array $map): array
    {
        $this->logger->info('Swivel - Parsing feature map.', compact('map'));

        return array_combine(array_keys($map), array_map([$this, 'reduceToBitmask'], $map));
    }
}
