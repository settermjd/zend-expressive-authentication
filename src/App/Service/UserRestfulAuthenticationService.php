<?php

namespace App\Service;

use App\Repository\UserAuthenticationInterface;
use GuzzleHttp\ClientInterface;

/**
 * Class UserRestfulAuthenticationService
 * @package App\Service
 */
class UserRestfulAuthenticationService implements UserAuthenticationInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * UserRestfulAuthenticationService constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * This will attempt to authenticate a user against a remote service
     * {@inheritdoc}
     *
     * @param string $username
     * @param string $password
     * @return int
     */
    public function authenticateUser($username, $password)
    {
        $result = json_decode($this->client->request('POST', '/auth', [
            'username' => $username,
            'password' => $password
        ]), true);

        return $result['userId'];
    }
}