<?php

namespace App\Entity;

interface AuthUserInterface
{
    public function getUsername();
    public function getPassword();
}