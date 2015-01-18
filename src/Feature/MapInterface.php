<?php
namespace Zumba\Swivel\Feature;

interface MapInterface {

    /**
     * Check if a feature slug is enabled for a particular bucket index
     *
     * @param string $slug
     * @param binary $index
     * @return boolean
     */
    public function enabled($slug, $index);
}
