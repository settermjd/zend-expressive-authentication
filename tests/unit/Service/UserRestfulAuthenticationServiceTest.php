<?php

namespace AppTest\Unit\Service;

use App\Service\UserRestfulAuthenticationService;
use GuzzleHttp\ClientInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Teapot\StatusCode\RFC\RFC2326;

class UserRestfulAuthenticationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientInterface|ObjectProphecy
     */
    private $remoteClient;

    public function setUp()
    {
        $this->remoteClient = $this->prophesize(ClientInterface::class);
    }

    public function testCanSuccessfullyAuthenticateValidUser()
    {
        // Mock a valid authentication request
        $this->remoteClient
            ->request('POST', '/auth', [
                'username' => 'matthew',
                'password' => 'setter'
            ])
            ->willReturn(json_encode(['userId' => 10000]))
            ->shouldBeCalledTimes(1);

        $service = new UserRestfulAuthenticationService($this->remoteClient->reveal());

        $this->assertSame(10000, $service->authenticateUser('matthew', 'setter'));
    }
}