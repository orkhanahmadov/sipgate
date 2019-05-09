<?php

namespace Orkhanahmadov\Sipgate;

use Orkhanahmadov\Sipgate\Exceptions\PropertyNotFoundException;

class User
{
    private $properties = [];

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function __get(string $name)
    {
        if (!isset($this->properties[$name])) {
            throw new PropertyNotFoundException($name);
        }

        return $this->properties[$name];
    }
}
