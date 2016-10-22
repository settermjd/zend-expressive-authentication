<?php

namespace App\Repository;

class UserTableAuthentication implements UserAuthenticationInterface
{
    /**
     * {@inheritdoc}
     */
    public function authenticateUser($username, $password)
    {
        return 1;
    }
}