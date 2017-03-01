<?php
/********************************
 * Author: M Holbrook-Bull
 * Date  : 01/03/2017
 ********************************/

namespace Ampisoft\UserBundle\Tests\Security;


use Ampisoft\UserBundle\Entity\AbstractGroup;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Ampisoft\UserBundle\Security\AmpUserProvider;
use Ampisoft\UserBundle\Tests\BaseSecurityTestCase;

class AmpUserProviderTest extends BaseSecurityTestCase
{

    public function setUp()
    {
        // user class
        /** @var AbstractUser user */
        $this->user = $this->createMock(User::class);
        $this->user->method('getApiToken')
                   ->will($this->returnValue('abcdef12345'));

        $this->user->method('getRoles')
                   ->will($this->returnValue(['ROLE_USER']));

        $this->group = $this->createMock(Group::class);

        parent::setUp();

    }

    public function testUserMethods()
    {

        $provider = new AmpUserProvider($this->manager, User::class);
        self::assertInstanceOf(AbstractUser::class, $provider->loadUserByUsername('test'));
        self::assertInstanceOf(AbstractUser::class, $provider->refreshUser($this->user));
        self::assertFalse($provider->supportsClass('test'));
        self::assertTrue(($provider->supportsClass(User::class)));



    }



}


class User extends AbstractUser
{

}

class Group extends AbstractGroup
{

}