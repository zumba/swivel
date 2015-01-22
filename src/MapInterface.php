<?php
namespace Zumba\Swivel;

interface MapInterface {

    /**
     * Check if a feature slug is enabled for a particular bucket index
     *
     * @param string $slug
     * @param binary $index
     * @return boolean
     */
    public function enabled($slug, $index);

    /**
     * Parse a human readable map into a map of bitmasks
     *
     * @param array $map
     * @return array
     */
    public function parse(array $map);
}
