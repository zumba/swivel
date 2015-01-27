<?php
namespace Tests\Metrics;

use \Zumba\Swivel\Metrics\NullReporter;

class NullReporterTest extends \PHPUnit_Framework_TestCase {

    /**
     * This test asserts that the null provider does not change it's state and does not return
     * anything when it's methods are executed.
     *
     * @dataProvider allMethodsProvider
     */
    public function testAllMethodsDoNothing($method) {
        $clean = new NullReporter();
        $test = new NullReporter();
        $this->assertNull($test->$method('test', 'source', function() {}));
        $this->assertEquals($clean, $test);
    }

    public function allMethodsProvider() {
        $image = new \ReflectionClass('Zumba\Swivel\Metrics\NullReporter');
        return array_map(function($reflection) { return [ $reflection->getName() ]; }, $image->getMethods());
    }
}
