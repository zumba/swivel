<?php

namespace Zumba\Swivel\Logging;

use \Psr\Log\NullLogger as PsrNullLogger;

/**
 * This Logger can be used to avoid conditional log calls
 *
 * Logging should always be optional, and if no logger is provided to your
 * library creating a NullLogger instance to have something to throw logs at
 * is a good way to avoid littering your code with `if ($this->logger) { }`
 * blocks. 
 * 
 * This logger extends the PSR Compliant NullLogger to implement __set_state()
 * This allows default support for var_export() compatable code generation.
 * Any logger you implement will need to return a PSR compliant logger instance 
 * In the __set_state() function for var_export() to work as expected.
 */
class NullLogger extends PsrNullLogger {

    /**
     * Set_state
     *
     * Support reloading class instance via var_export generated code
     * 
     * This method is used to rebuild a valid implementation of the parent logger class.
     * You get an array of class properties and data that you use to create a new instance
     * And return back to the calling code. 
     *
     * The NullLogger interface returns null, so we do that here.
     * 
     * @param array $objData Array of logger data needed to reconsturct logger class
     * @return string           Implementaiton of logger class to be passed to the Map class
     */
    public static function __set_state($objData = array()) {
        return null;
    }
}
