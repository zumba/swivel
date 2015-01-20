<?php
namespace Zumba\Swivel;

interface ConfigInterface {

    /**
     * Add a feature to the config.
     *
     * @param string $slug
     * @param array $buckets
     * @return void
     */
    public function addFeature($slug, array $buckets);

    /**
     * Add an array of features to the config.
     *
     * @param array $map Example: [ "A" => [1,2,3], "B" => [4,5,6] ]
     * @return void
     */
    public function addFeatures(array $map);

    /**
     * Get a configured Bucket instance
     *
     * @return \Zumba\Swivel\Bucket
     */
    public function getBucket();

    /**
     * Get the configured feature map.
     *
     * @return array
     */
    public function getMap();

    /**
     * Remove a slug from the map.
     *
     * If $buckets is provided, will only remove the indicated buckets from the feature, not the
     * entire slug.
     *
     * @param string $slug
     * @param array $buckets
     * @return void
     */
    public function removeFeature($slug, array $buckets = []);

    /**
     * Set the bucket index for the user
     *
     * @param integer $index
     * @return void
     */
    public function setBucket($index);
}
