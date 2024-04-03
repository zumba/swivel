<?php

namespace Zumba\Swivel;

interface DriverInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * Generate a Zumba\Swivel\MapInterface object.
     */
    public function getMap(): MapInterface;
}
