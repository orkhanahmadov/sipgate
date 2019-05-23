<?php

namespace Orkhanahmadov\Sipgate\Auth;

use GuzzleHttp\Client;

class OAuth implements Auth
{
    /**
     * @var string
     */
    private $clientId;
    /**
     * @var string
     */
    private $clientSecret;
    /**
     * @var Client
     */
    private $client;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->client = new Client();
    }

//    public function auth(string $redirectUri, string $scope = 'all')
//    {
//        $response = $this->client->get(
//            'https://login.sipgate.com/auth/realms/third-party/protocol/openid-connect/auth',
//            [
//                'query' => [
//                    'client_id'     => $this->clientId,
//                    'redirect_uri'  => $redirectUri,
//                    'scope'         => $scope,
//                    'response_type' => 'code',
//                ],
//            ]
//        );
//    }

    public function requestToken(string $code, string $redirectUri): Token
    {
        $response = $this->client->post(
            'https://login.sipgate.com/auth/realms/third-party/protocol/openid-connect/token',
            [
                'form_params' => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code'          => $code,
                    'redirect_uri'  => $redirectUri,
                    'grant_type'    => 'authorization_code',
                ],
            ]
        );

        return new Token(\GuzzleHttp\json_decode($response->getBody()->getContents(), true));
    }

    public function refreshToken(string $refreshToken): Token
    {
        $response = $this->client->post(
            'https://login.sipgate.com/auth/realms/third-party/protocol/openid-connect/token',
            [
                'form_params' => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type'    => 'refresh_token',
                ],
            ]
        );

        return new Token(\GuzzleHttp\json_decode($response->getBody()->getContents(), true));
    }
}
