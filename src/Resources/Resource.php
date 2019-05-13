<?php

namespace Orkhanahmadov\Sipgate\Resources;

use JsonSerializable;
use Orkhanahmadov\Sipgate\Exceptions\ResourcePropertyNotFoundException;

abstract class Resource implements JsonSerializable
{
    /**
     * @var array
     */
    private $properties = [];

    /**
     * Resource constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    /**
     * @param string $name
     *
     * @throws ResourcePropertyNotFoundException
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (!isset($this->properties[$name])) {
            throw new ResourcePropertyNotFoundException($name);
        }

        return $this->properties[$name];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->properties;
    }
}
