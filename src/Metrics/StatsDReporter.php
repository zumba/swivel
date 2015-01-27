<?php
namespace Zumba\Swivel\Metrics;

use \Domnikl\Statsd\Client,
    \Cocur\Slugify\Slugify;

class StatsDReporter implements \Zumba\Swivel\MetricsInterface {

    const DEFAULT_NAMESPACE = 'Swivel';
    const SLUG_DELIMITER = '.';
    const SOURCE_DELIMITER = '_';
    const SLUG_REGEX = '/([^a-z0-9_]|\.|_{2,})+/';

    /**
     * StatsD client
     *
     * @var \Domnikl\Statsd\Client
     */
    protected $client;

    /**
     * Metrics namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Cache of formatted slugs.
     *
     * @var array
     */
    protected $slugCache = [];

    /**
     * Used to massage feature slugs
     *
     * @var \Cocur\Slugify\Slugify
     */
    protected $slugify;

    /**
     * Zumba\Swivel\Metrics\StatsD
     *
     * @param \Domnikl\Statsd\Client $client
     * @param \Cocur\Slugify\Slugify|null $slugify
     */
    public function __construct(Client $client, Slugify $slugify = null) {
        $this->slugify = $slugify ?: new Slugify(static::SLUG_REGEX);
        $this->client = $client;
        $this->setNamespace();
    }

    /**
     * Send a count
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function count($context, $source, $value = 1, $metric = '') {
        $this->client->count($this->slug($context, $source, 'count'), $value);
    }

    /**
     * Decrements a metric by 1
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function decrement($context, $source, $metric = '') {
        $this->client->decrement($this->slug($context, $source, 'count'));
    }

    /**
     * End the memory profiling and send the value
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function endMemoryProfile($context, $source, $metric = '') {
        $this->client->endMemoryProfile($this->slug($context, $source, 'memory'));
    }

    /**
     * End the timing for a metric and send the value
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function endTiming($context, $source, $metric = '') {
        $this->client->endTiming($this->slug($context, $source, 'timing'));
    }

    /**
     * Send a gauged metric
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function gauge($context, $source, $value = 0, $metric = '') {
        $this->client->gauge($this->slug($context, $source, 'gauge'), $value);
    }

    /**
     * Increments the metric by 1
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function increment($context, $source, $metric = '') {
        $this->client->increment($this->slug($context, $source, 'count'));
    }

    /**
     * Report memory usage.
     *
     * If $memory is null, report peak usage
     *
     * @param string $context
     * @param string $source
     * @param integer|null $memory
     * @param string $metric
     * @return void
     */
    public function memory($context, $source, $memory = null, $metric = '') {
        $this->client->memory($this->slug($context, $source, 'memory'), $memory);
    }

    /**
     * Send a unique metric
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function set($context, $source, $value = 0, $metric = '') {
        $this->client->set($this->slug($context, $source, 'set'), $value);
    }

    /**
     * Set the slug namespace
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace = self::DEFAULT_NAMESPACE) {
        $this->namespace = (string)$namespace;
    }

    /**
     * Generate the StatsD bucket slug
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return string
     */
    protected function slug($context, $source, $metric) {
        $key = $context . $source . $metric;
        if (isset($this->slugCache[$key])) {
            return $this->slugCache[$key];
        }

        $source = $this->slugify->slugify($source, static::SOURCE_DELIMITER);
        $context = implode(static::SLUG_DELIMITER, [$this->namespace, $context, $metric, $source]);
        $this->slugCache[$key] = $this->slugify->slugify($context, static::SLUG_DELIMITER);

        return $this->slugCache[$key];
    }

    /**
     * Start memory "profiling"
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function startMemoryProfile($context, $source, $metric = '') {
        $this->client->startMemoryProfile($this->slug($context, $source, 'memory'));
    }

    /**
     * Starts timing a metric
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function startTiming($context, $source, $metric = '') {
        $this->client->startTiming($this->slug($context, $source, 'timing'));
    }

    /**
     * Execute, measure execution time, and return a \Closure's return value
     *
     * @param string $context
     * @param string $source
     * @param \Closure $func
     * @param string $metric
     * @return mixed
     */
    public function time($context, $source, \Closure $func, $metric = '') {
        return $this->client->time($this->slug($context, $source, 'timing'), $func);
    }

    /**
     * Send a timing metric
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function timing($context, $source, $value = 0, $metric = '') {
        $this->client->timing($this->slug($context, $source, 'timing'), $value);
    }
}
