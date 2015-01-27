<?php
namespace Zumba\Swivel\Metrics;

class NullReporter implements \Zumba\Swivel\MetricsInterface {

    /**
     * Zumba\Swivel\Metrics\StatsD
     */
    public function __construct() {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function count($context, $source, $value = 1, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function decrement($context, $source, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function endMemoryProfile($context, $source, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function endTiming($context, $source, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function gauge($context, $source, $value = 0, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function increment($context, $source, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
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
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function set($context, $source, $value = 0, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace = self::DEFAULT_NAMESPACE) {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function startMemoryProfile($context, $source, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     * @return void
     */
    public function startTiming($context, $source, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param \Closure $func
     * @param string $metric
     * @return mixed
     */
    public function time($context, $source, \Closure $func, $metric = '') {
        // no op
    }

    /**
     * Do nothing.
     *
     * @param string $context
     * @param string $source
     * @param integer $value
     * @param string $metric
     * @return void
     */
    public function timing($context, $source, $value = 0, $metric = '') {
        // no op
    }
}
