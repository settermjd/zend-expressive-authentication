<?php

namespace App\Service;

use App\Repository\UserAuthenticationException;
use App\Repository\UserAuthenticationInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;

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
        try {
            $response = $this->client->request('POST', '/auth', [
                'form_params' => [
                    'username' => $username,
                    'password' => $password
                ],
                'debug' => true
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                throw new UserAuthenticationException(
                    $e->getResponse()->getBody()->getContents(),
                    (int)$e->getResponse()->getStatusCode()
                );
            }
        }

        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getBody()->getContents(), true);

            if (is_array($body) && isset($body['userId'])) {
                return (int)$body['userId'];
            }
        }
    }
}