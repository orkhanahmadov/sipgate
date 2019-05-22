<?php

namespace Orkhanahmadov\Sipgate\Auth;

interface Auth
{
    /**
     * Requests token from sipgate oauth endpoint.
     *
     * @param string $code
     * @param string $redirectUri
     *
     * @return Token
     */
    public function requestToken(string $code, string $redirectUri): Token;
}
