<?php
namespace Tests\Feature;

use \Zumba\Swivel\Feature\Map;
use \Zumba\Swivel\Feature;
use \Zumba\Swivel\Bucket;

class MapTest extends \PHPUnit_Framework_TestCase {

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
                    'Test' => Bucket::FIRST,
                    'Test.version' => Bucket::FIRST,
                    'Test.version.a' => Bucket::FIRST
                ]
            ],
            [
                'assertTrue', 'Test.version', Bucket::FIRST, [
                    'Test' => Bucket::FIRST,
                    'Test.version' => Bucket::FIRST,
                    'Test.version.a' => Bucket::FIRST
                ]
            ],
            [
                'assertTrue', 'Test', Bucket::FIRST, [
                    'Test' => Bucket::FIRST,
                    'Test.version' => Bucket::FIRST,
                    'Test.version.a' => Bucket::FIRST
                ]
            ],
            [
                'assertTrue', 'Test.version', Bucket::FIRST, [
                    'Test' => Bucket::FIRST,
                    'Test.version' => Bucket::FIRST,
                    'Test.version.a' => Feature::OFF
                ]
            ],
            [
                'assertFalse', 'Test.version.a', Bucket::FIRST, [
                    'Test' => Feature::OFF,
                    'Test.version' => Bucket::FIRST,
                    'Test.version.a' => Bucket::FIRST
                ]
            ],
            [
                'assertFalse', 'Test.version.a', Bucket::FIRST, [
                    'Test' => Bucket::FIRST,
                    'Test.version' => Feature::OFF,
                    'Test.version.a' => Bucket::FIRST
                ]
            ],
            [
                'assertFalse', 'Test.version.b', Bucket::FIRST, [
                    'Test' => Bucket::FIRST,
                    'Test.version' => Bucket::FIRST,
                    'Test.version.a' => Bucket::FIRST
                ]
            ],
            [
                'assertTrue', 'Test.version.a', Bucket::THIRD, [
                    'Test' => Bucket::SECOND | Bucket::THIRD | Bucket::FIFTH,
                    'Test.version' => Bucket::SECOND | Bucket::THIRD,
                    'Test.version.a' => Bucket::THIRD
                ]
            ],
        ];
    }
}
