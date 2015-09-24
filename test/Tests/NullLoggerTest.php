<?php
namespace Tests;

use \Zumba\Swivel\Logging\NullLogger;

class NullLoggerTest extends \PHPUnit_Framework_TestCase {

    public function testSetStateNoData() {
        $nullLogger = NullLogger::__set_state([]);
        $this->assertNull($nullLogger);
    }

    public function testSetStateData() {
        $nullLogger = NullLogger::__set_state(['some' => 'data']);
        $this->assertNull($nullLogger);
    }
}
