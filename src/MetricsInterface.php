<?php

namespace Zumba\Swivel;

use Closure;

interface MetricsInterface
{
    /**
     * Namespace used as a default.
     */
    const DEFAULT_NAMESPACE = 'Swivel';

    /**
     * Send a count.
     */
    public function count(string $context, string $source, int $value = 1, string $metric = ''): void;

    /**
     * Decrement a metric by 1.
     */
    public function decrement(string $context, string $source, string $metric = ''): void;

    /**
     * End the memory profiling and send the value.
     */
    public function endMemoryProfile(string $context, string $source, string $metric = ''): void;

    /**
     * End the timing for a metric and send the value.
     */
    public function endTiming(string $context, string $source, string $metric = ''): void;

    /**
     * Send a gauged metric.
     */
    public function gauge(string $context, string $source, int $value = 0, string $metric = ''): void;

    /**
     * Increment the metric by 1.
     */
    public function increment(string $context, string $source, string $metric = ''): void;

    /**
     * Report memory usage.
     *
     * If $memory is null, report peak usage
     */
    public function memory(string $context, string $source, ?int $memory = null, string $metric = ''): void;

    /**
     * Send a unique metric.
     */
    public function set(string $context, string $source, int $value = 0, string $metric = ''): void;

    /**
     * Set the slug namespace.
     */
    public function setNamespace(string $namespace = self::DEFAULT_NAMESPACE): void;

    /**
     * Start memory "profiling".
     */
    public function startMemoryProfile(string $context, string $source, string $metric = ''): void;

    /**
     * Starts timing a metric.
     */
    public function startTiming(string $context, string $source, string $metric = ''): void;

    /**
     * Execute, measure execution time, and return a \Closure's return value.
     */
    public function time(string $context, string $source, Closure $func, string $metric = ''): mixed;

    /**
     * Send a timing metric.
     */
    public function timing(string $context, string $source, int $value = 0, string $metric = ''): void;
}
