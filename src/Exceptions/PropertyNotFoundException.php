<?php

namespace Orkhanahmadov\Sipgate\Exceptions;

use Exception;

class PropertyNotFoundException extends Exception
{
    public function __construct($property)
    {
        parent::__construct($property.' property not found.');
    }
}
