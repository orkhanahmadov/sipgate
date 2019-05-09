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
     * @var User
     */
    public $user;

    public function __construct(User $user, array $properties = [])
    {
        parent::__construct($properties);

        $this->user = $user;
    }
}
