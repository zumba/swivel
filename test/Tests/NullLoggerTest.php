<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Zumba\Swivel\Logging\NullLogger;

class NullLoggerTest extends TestCase
{
    public function testSetStateNoData()
    {
        $nullLogger = NullLogger::__set_state([]);
        $this->assertInstanceOf(NullLogger::class, $nullLogger);
    }

    public function testSetStateData()
    {
        $nullLogger = NullLogger::__set_state(['some' => 'data']);
        $this->assertInstanceOf(NullLogger::class, $nullLogger);
    }
}
