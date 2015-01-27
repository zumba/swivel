<?php
namespace Tests\Metrics;

use \Zumba\Swivel\Metrics\StatsDReporter;
use \Domnikl\Statsd\Client;
use \Domnikl\Statsd\Connection\Blackhole;
use \Cocur\Slugify\Slugify;

class StatsDTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider delegateProvider
     */
    public function testDelegateToClient($method, $slug, $source, $sluggified, $value) {
        $client = $this->getMock('Domnikl\Statsd\Client', [], [new Blackhole()]);
        $expectation = $client
            ->expects($this->once())
            ->method($method);

        if ($value) {
            $expectation->with($sluggified, $value);
        } else {
            $expectation->with($sluggified);
        }

        $statsd = new StatsDReporter($client);
        $statsd->$method($slug, $source, $value);
    }

    public function delegateProvider() {
        return [
            [ 'count', 'Features', 'Search.Version.Test', 'swivel.features.count.search_version_test', 2 ],
            [ 'decrement', 'Features', 'Search.Version.Test', 'swivel.features.count.search_version_test', 1 ],
            [ 'endMemoryProfile', 'Features', 'Search.Version.Test', 'swivel.features.memory.search_version_test', 1 ],
            [ 'endTiming', 'Features', 'Search.Version.Test', 'swivel.features.timing.search_version_test', 1 ],
            [ 'gauge', 'Features', 'Search.Version.Test', 'swivel.features.gauge.search_version_test', 345 ],
            [ 'increment', 'Features', 'Search.Version.Test', 'swivel.features.count.search_version_test', 1 ],
            [ 'memory', 'Features', 'Search.Version.Test', 'swivel.features.memory.search_version_test', 1 ],
            [ 'set', 'Features', 'Search.Version.Test', 'swivel.features.set.search_version_test', 9876 ],
            [ 'startMemoryProfile', 'Features', 'Search.Version.Test', 'swivel.features.memory.search_version_test', null ],
            [ 'startTiming', 'Features', 'Search.Version.Test', 'swivel.features.timing.search_version_test', null ],
            [ 'time', 'Features', 'Search.Version.Test', 'swivel.features.timing.search_version_test', function() {} ],
            [ 'timing', 'Features', 'Search.Version.Test', 'swivel.features.timing.search_version_test', 1 ]
        ];
    }

    public function testSlugsAreCached() {
        $client = $this->getMock('Domnikl\Statsd\Client', ['count'], [new Blackhole()]);
        $slugify = $this->getMock('Cocur\Slugify\Slugify', ['slugify'], [StatsDReporter::SLUG_REGEX]);
        $client
            ->expects($this->exactly(5))
            ->method('count')
            ->with('swivel.features.count.slug_cache_test', 1);

        $slugify
            ->expects($this->exactly(2))
            ->method('slugify')
            ->withConsecutive(
                ['Slug.Cache.Test', StatsDReporter::SOURCE_DELIMITER],
                ['Swivel.Features.count.slug_cache_test', StatsDReporter::SLUG_DELIMITER]
            )
            ->will($this->returnCallback(function($arg1) {
                return $arg1 === 'Slug.Cache.Test'
                    ? 'slug_cache_test'
                    : 'swivel.features.count.slug_cache_test';
            }));

        $statsd = new StatsDReporter($client, $slugify);
        for ($i = 0; $i < 5; $i++) {
            $statsd->count('Features', 'Slug.Cache.Test', 1);
        }
    }
}
