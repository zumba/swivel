<?php

namespace Zumba\Swivel;

interface MetricsInterface
{
    /**
     * Namespace used as a default.
     */
    const DEFAULT_NAMESPACE = 'Swivel';

    /**
     * Send a count.
     *
     * @param string $context
     * @param string $source
     * @param int    $value
     * @param string $metric
     */
    public function count($context, $source, $value = 1, $metric = '');

    /**
     * Decrement a metric by 1.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     */
    public function decrement($context, $source, $metric = '');

    /**
     * End the memory profiling and send the value.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     */
    public function endMemoryProfile($context, $source, $metric = '');

    /**
     * End the timing for a metric and send the value.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     */
    public function endTiming($context, $source, $metric = '');

    /**
     * Send a gauged metric.
     *
     * @param string $context
     * @param string $source
     * @param int    $value
     * @param string $metric
     */
    public function gauge($context, $source, $value = 0, $metric = '');

    /**
     * Increment the metric by 1.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     */
    public function increment($context, $source, $metric = '');

    /**
     * Report memory usage.
     *
     * If $memory is null, report peak usage
     *
     * @param string   $context
     * @param string   $source
     * @param int|null $memory
     * @param string   $metric
     */
    public function memory($context, $source, $memory = null, $metric = '');

    /**
     * Send a unique metric.
     *
     * @param string $context
     * @param string $source
     * @param int    $value
     * @param string $metric
     */
    public function set($context, $source, $value = 0, $metric = '');

    /**
     * Set the slug namespace.
     *
     * @param string $namespace
     */
    public function setNamespace($namespace = self::DEFAULT_NAMESPACE);

    /**
     * Start memory "profiling".
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     */
    public function startMemoryProfile($context, $source, $metric = '');

    /**
     * Starts timing a metric.
     *
     * @param string $context
     * @param string $source
     * @param string $metric
     */
    public function startTiming($context, $source, $metric = '');

    /**
     * Execute, measure execution time, and return a \Closure's return value.
     *
     * @param string   $context
     * @param string   $source
     * @param \Closure $func
     * @param string   $metric
     *
     * @return mixed
     */
    public function time($context, $source, \Closure $func, $metric = '');

    /**
     * Send a timing metric.
     *
     * @param string $context
     * @param string $source
     * @param int    $value
     * @param string $metric
     */
    public function timing($context, $source, $value = 0, $metric = '');
}
