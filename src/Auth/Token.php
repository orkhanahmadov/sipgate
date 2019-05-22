<?php

namespace Orkhanahmadov\Sipgate\Auth;

class Token
{
    /**
     * @var string
     */
    public $sessionState;
    /**
     * @var string
     */
    public $tokenType;
    /**
     * @var string
     */
    public $idToken;
    /**
     * @var string
     */
    public $refreshToken;
    /**
     * @var string
     */
    public $accessToken;
    /**
     * @var string
     */
    public $notBeforePolicy;
    /**
     * @var string
     */
    public $expiresIn;
    /**
     * @var string
     */
    public $refreshExpiresIn;

    public function __construct(array $data)
    {
        $this->sessionState = $data['session_state'];
        $this->tokenType = $data['token_type'];
        $this->idToken = $data['id_token'];
        $this->refreshToken = $data['refresh_token'];
        $this->accessToken = $data['access_token'];
        $this->notBeforePolicy = $data['not-before-policy'];
        $this->expiresIn = $data['expires_in'];
        $this->refreshExpiresIn = $data['refresh_expires_in'];
    }
}
