<?php

namespace Innoscripta\Sipgate\Exceptions;

use Exception;

class ResourcePropertyNotFoundException extends Exception
{
    /**
     * ResourcePropertyNotFoundException constructor.
     *
     * @param $property
     */
    public function __construct($property)
    {
        parent::__construct($property.' property not found.');
    }
}
