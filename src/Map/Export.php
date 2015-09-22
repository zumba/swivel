<?php
namespace Zumba\Swivel\Map;

use Zumba\Swivel\Map;

abstract class Export {

    const LOGGER_REMOVE_REGEX = '~\'logger\'(?:\s)?=>(?:\s+)?(?:[\w\\\\]+)::__set_state(?:\([^$]+),~';

    public static function export(Map $map)
    {
        $exported = var_export($map, true);
        return preg_replace(
            self::LOGGER_REMOVE_REGEX,
            '',
            $exported
        );
    }
}