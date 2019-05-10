<?php

namespace Orkhanahmadov\Sipgate\Resources;

/**
 * @property string id
 * @property string alias
 * @property string type
 * @property bool online
 * @property bool dnd
 * @property array activePhonelines
 * @property array activeGroups
 * @property array credentials
 * @property array registered
 * @property string emergencyAddressId
 * @property string addressUrl
 */
class Device extends Resource
{
    /**
     * @var User|string
     */
    public $user;

    /**
     * Device constructor.
     * @param User|string $user
     * @param array $properties
     */
    public function __construct($user, array $properties = [])
    {
        parent::__construct($properties);

        $this->user = $user;
    }

    public function userId()
    {
        return $this->user instanceof User ? $this->user->id : $this->user;
    }
}
