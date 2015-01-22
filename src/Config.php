<?php
namespace Zumba\Swivel;

class Config implements ConfigInterface {

    /**
     * Index of the user's bucket
     *
     * @var integer
     */
    protected $index;

    /**
     * Map of features
     *
     * @var array
     */
    protected $map;

    /**
     * Zumba\Swivel\Config
     *
     * @param array $initialMap
     * @param integer|null $index
     */
    public function __construct(array $initialMap = [], $index = null) {
        $this->map = $initialMap;
        $this->index = $index;
    }

    /**
     * Add a feature to the config.
     *
     * @param string $slug
     * @param array $buckets
     * @return void
     */
    public function addFeature($slug, array $buckets) {
        if (empty($this->map[$slug])) {
            $this->map[$slug] = [];
        }
        $this->map[$slug] = array_merge($this->map[$slug], $buckets);
    }

    /**
     * Add an array of features to the config.
     *
     * @param array $map Example: [ "A" => [1,2,3], "B" => [4,5,6] ]
     * @return void
     */
    public function addFeatures(array $map) {
        foreach ($map as $slug => $buckets) {
            $this->addFeature($slug, $buckets);
        }
    }

    /**
     * Get a configured Bucket instance
     *
     * @return \Zumba\Swivel\Bucket
     */
    public function getBucket() {
        $map = new Map($this->getMap());
        return new Bucket($map, $this->index);
    }

    /**
     * Get the configured feature map.
     *
     * @return array
     */
    public function getMap() {
        return $this->map;
    }

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
    public function removeFeature($slug, array $buckets = []) {
        if (empty($buckets)) {
            unset($this->map[$slug]);
        } else if (isset($this->map[$slug])) {
            $this->map[$slug] = array_values(array_diff($this->map[$slug], $buckets));
        }
    }

    /**
     * Set the bucket index for the user
     *
     * @param integer $index
     * @return void
     */
    public function setBucket($index) {
        $this->index = $index;
    }
}
