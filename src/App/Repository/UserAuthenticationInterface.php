<?php

namespace App\Repository;

interface UserAuthenticationInterface
{
    /**
     * @param string $username
     * @param string $password
     * @return int
     * @throws UserAuthenticationException
     */
    public function authenticateUser($username, $password);
}