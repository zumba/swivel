<?php
namespace Tests\Feature;

use \Zumba\Swivel\Feature\Map;
use \Zumba\Swivel\Feature;
use \Zumba\Swivel\Bucket;

class MapTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider parseProvider
     */
    public function testParse($map, $expected) {
        $featureMap = new Map($map);
        $this->assertEquals($expected, $featureMap->parse($map));
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
            ]
        ];
    }

    /**
     * @dataProvider enabledProvider
     */
    public function testEnabled($assertion, $slug, $index, $map) {
        $featureMap = new Map($map);
        $this->$assertion($featureMap->enabled($slug, $index));
    }

    public function enabledProvider() {
        return [
            [
                'assertTrue', 'Test.version.a', Bucket::FIRST, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertTrue', 'Test.version', Bucket::FIRST, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertTrue', 'Test', Bucket::FIRST, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertTrue', 'Test.version', Bucket::FIRST, [
                    'Test' => [1],
                    'Test.version' => [1],
                    'Test.version.a' => []
                ]
            ],
            [
                'assertFalse', 'Test.version.a', Bucket::FIRST, [
                    'Test' => [],
                    'Test.version' => [1],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertFalse', 'Test.version.a', Bucket::FIRST, [
                    'Test' => [1],
                    'Test.version' => [],
                    'Test.version.a' => [1]
                ]
            ],
            [
                'assertFalse', 'Test.version.b', Bucket::FIRST, [
                    'Test' => [],
                    'Test.version' => [],
                    'Test.version.a' => []
                ]
            ],
            [
                'assertTrue', 'Test.version.a', Bucket::THIRD, [
                    'Test' => [2, 3, 5],
                    'Test.version' => [2, 3],
                    'Test.version.a' => [3]
                ]
            ],
        ];
    }
}
