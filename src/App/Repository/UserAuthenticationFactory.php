<?php

namespace App\Repository;

use Interop\Container\ContainerInterface;

class UserAuthenticationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new UserTableAuthentication();
    }
}