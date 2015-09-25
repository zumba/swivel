<?php
namespace Tests;

use \Zumba\Swivel\Map;
use \Zumba\Swivel\Bucket;

class MapTest extends \PHPUnit_Framework_TestCase {

    public function testNoLoggerAfterSerialization()
    {
        $serializedMap = serialize(new Map(['feature.test' => [1,2,3]]));
        $this->assertNotContains('logger', $serializedMap);
    }

    /**
     * @depends testNoLoggerAfterSerialization
     */
    public function testLoggerInPlaceAfterUnserialization()
    {
        $serializedMap = serialize(new Map(['feature.test' => [1,2,3]]));
        $map = unserialize($serializedMap);

        $reflObject = new \ReflectionObject($map);
        $loggerReflProperty = $reflObject->getProperty('logger');
        $loggerReflProperty->setAccessible(true);
        $logger = $loggerReflProperty->getValue($map);
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $logger);
    }

    /**
     * @dataProvider parseProvider
     */
    public function testParse($map, $expected) {
        $featureMap = new Map($map);
        $this->assertEquals($expected, $featureMap->parse($map));
    }

    /**
     * @dataProvider enabledProvider
     */
    public function testEnabled($assertion, $slug, $index, $map) {
        $featureMap = new Map($map);
        $this->$assertion($featureMap->enabled($slug, $index));
    }

    /**
     * @dataProvider diffProvider
     */
    public function testDiff($a, $b, $expected) {
        $map1 = new Map($a);
        $map2 = new Map($b);
        $this->assertEquals($expected, $map1->diff($map2)->getMapData());
    }

    /**
     * @dataProvider mergeProvider
     */
    public function testMerge($a, $b, $expected) {
        $map1 = new Map($a);
        $map2 = new Map($b);
        $this->assertEquals($expected, $map1->merge($map2)->getMapData());
    }

    /**
     * @dataProvider intersectProvider
     */
    public function testIntersect($a, $b, $expected) {
        $map1 = new Map($a);
        $map2 = new Map($b);
        $this->assertEquals($expected, $map1->intersect($map2)->getMapData());
    }

    /**
     * @dataProvider addProvider
     */
    public function testAdd($a, $b, $expected) {
        $map1 = new Map($a);
        $map2 = new Map($b);
        $this->assertEquals($expected, $map1->add($map2)->getMapData());
    }

    /**
     * @dataProvider addProvider
     */
    public function testVarExport($a, $b, $expected) {
        $map1 = new Map($a);
        $map2 = new Map($b);

        $code1 = var_export($map1, true);
        $code2 = var_export($map2, true);

        eval('$map_eval_1 = '.$code1.';');
        eval('$map_eval_2 = '.$code2.';');

        $this->assertEquals($expected, $map_eval_1->add($map_eval_2)->getMapData());

        $this->assertTrue($map_eval_1->enabled('a', 1));
    }

    public function parseProvider() {
        return [
            [
                [ 'a' => [1] ],
                [ 'a' => Bucket::FIRST ]
            ],
            [
                [ 'a' => [1, 2] ],
                [ 'a' => Bucket::FIRST | Bucket::SECOND ]
            ],
            [
                [ 'a' => [6, 7], 'a.b' => [7] ],
                [ 'a' => Bucket::SIXTH | Bucket::SEVENTH, 'a.b' => Bucket::SEVENTH ]
            ],
            [
                [ 'a' => Bucket::FIRST ],
                [ 'a' => Bucket::FIRST ]
            ]
        ];
    }

    public function enabledProvider() {
        return [
            [
                'assertTrue', 'Test.version.a', 1, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertTrue', 'Test.version', 1, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertTrue', 'Test', 1, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertTrue', 'Test.version', 1, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => []
                ]
            ],
            [
                'assertFalse', 'Test.version.a', 1, [
                    'Test' => [],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertFalse', 'Test.version.a', 1, [
                    'Test' => [1],
                    'Test.version' => [],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertFalse', 'Test.version.b', 1, [
                    'Test' => [],
                    'Test.version' => [],
                    'Test.version.a' => []
                ]
            ],
            [
                'assertTrue', 'Test.version.a', 3, [
                    'Test' => [2, 3, 5],
                    'Test.version' => [2, 3],
                    'Test.version.a' => [3]
                ]
            ],
        ];
    }

    public function diffProvider() {
        return [
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                []
            ],
            [
                [ 'a' => [1,2], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'c' => [7] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'c' => Bucket::SEVENTH ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'd' => [1] ],
                [ 'd' => Bucket::FIRST ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'c' => [7] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'd' => [1] ],
                [ 'd' => Bucket::FIRST, 'c' => Bucket::SEVENTH ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [], 'b' => [] ],
                [ 'a' => 0, 'b' => 0 ]
            ]
        ];
    }

    public function intersectProvider() {
        return [
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH
                ]
            ],
            [
                [ 'a' => [1,2], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'c' => [7] ],
                [ 'a' => [2,3,4], 'b' => [4,5,6] ],
                [ 'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [9,8,7], 'd' => [1] ],
                [ 'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD ]
            ]
        ];
    }

    public function mergeProvider() {
        return [
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH
                ]
            ],
            [
                [ 'a' => [1,2], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH
                ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'c' => [7] ],
                [ 'a' => [2,3,4], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::SECOND | Bucket::THIRD | Bucket::FOURTH,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH,
                    'c' => Bucket::SEVENTH
                ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [9,8,7], 'd' => [1] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD,
                    'b' => Bucket::NINTH | Bucket::EIGHTH | Bucket::SEVENTH,
                    'd' => Bucket::FIRST
                ]
            ]
        ];
    }

    public function addProvider() {
        return [
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH
                ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6] ],
                [ 'a' => [3,4,5], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD | Bucket::FOURTH | Bucket::FIFTH,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH
                ]
            ],
            [
                [ 'a' => [1,2,3], 'b' => [4,5,6], 'c' => [7] ],
                [ 'a' => [2,3,4], 'b' => [4,5,6] ],
                [
                    'a' => Bucket::FIRST | Bucket::SECOND | Bucket::THIRD | Bucket::FOURTH,
                    'b' => Bucket::FOURTH | Bucket::FIFTH | Bucket::SIXTH,
                    'c' => Bucket::SEVENTH
                ]
            ]
        ];
    }
}
