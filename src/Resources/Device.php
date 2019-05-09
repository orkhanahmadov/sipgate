<?php

namespace Orkhanahmadov\Sipgate\Resources;

class Device extends Resource
{
    /**
     * @var User
     */
    public $user;

    public function __construct(User $user, array $properties = [])
    {
        parent::__construct($properties);

        $this->user = $user;
    }
}
