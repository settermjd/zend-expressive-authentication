<?php

namespace AppTest\Unit\Service;

use App\Service\UserRestfulAuthenticationService;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Prophecy\Prophecy\ObjectProphecy;

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

    /**
     * @expectedException \App\Service\UserNotFoundException
     */
    public function testCanHandleInvalidUserAuthenticationAttempt()
    {
        $exception = $this->prophesize(ClientException::class);

        // Mock a valid authentication request
        $this->remoteClient
            ->request('POST', '/auth', [
                'username' => 'unknown',
                'password' => 'user'
            ])
            ->willThrow($exception->reveal())
            ->shouldBeCalledTimes(1);

        $service = new UserRestfulAuthenticationService($this->remoteClient->reveal());
        $service->authenticateUser('unknown', 'user');
    }
}