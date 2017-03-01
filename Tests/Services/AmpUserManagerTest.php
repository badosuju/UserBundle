<?php
/********************************
 * Author: M Holbrook-Bull
 * Date  : 27/02/2017
 ********************************/


use Ampisoft\UserBundle\Services\AmpUserManager;
use Ampisoft\UserBundle\Tests\BaseSecurityTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder as PasswordEncoder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use PHPUnit\Framework\TestCase;

/**
 * Class AmpUserManagerTest
 * @author M Holbrook-Bull
 */
class AmpUserManagerTest extends BaseSecurityTestCase
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


    public function testCreateUser()
    {

        self::assertInstanceOf(User::class, $this->manager->loadUser('test'));
        self::assertInstanceOf(User::class, $this->manager->newUser());

        $group = $this->manager->createUserGroup('testGroup');
        self::assertInstanceOf(Group::class, $group);
        self::assertEquals('testGroup', $group->getName());

        $user = $this->manager->createUser('testUser', 'password', 'email@gmail.com', 'testgroup');
        self::assertInstanceOf(User::class, $user);
    }

    public function testUserUpdate()
    {
        $user = new User();
        $user->setPlainPassword('test');
        $user = $this->manager->updateUser($user);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals(null, $user->getPlainPassword());
        self::assertEquals('thisisencodedhonest', $user->getPassword());
    }

    public function testLoginUser()
    {
        $this->manager->loginUser($this->user);
        self::assertInstanceOf(\Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken::class, $this->tokenStorage->getToken());
    }

}


class Group extends \Ampisoft\UserBundle\Entity\AbstractGroup
{

}

class User extends AbstractUser
{
}

