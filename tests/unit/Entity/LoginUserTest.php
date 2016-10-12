<?php

use App\Entity\LoginUser;

class LoginUserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LoginUser::populate()
     */
    public function testCanHydrateEntity()
    {
        $entity = new LoginUser();
        $data = [
            'username' => 'user',
            'password' => 'password'
        ];
        $entity->populate($data);
        $this->assertSame($entity->getUsername(), $data['username'], 'expected username differs');
        $this->assertSame($entity->getPassword(), $data['password'], 'expected password differs');
    }

    /**
     * @covers LoginUser::getArrayCopy()
     */
    public function testCanGetArrayCopy()
    {
        $entity = new LoginUser();
        $data = [
            'username' => 'user',
            'password' => 'password'
        ];
        $entity->populate($data);
        $this->assertSame(
            $entity->getArrayCopy(),
            array_merge($data, ['submit' => null]),
            'expected array copy differs'
        );
    }
}
