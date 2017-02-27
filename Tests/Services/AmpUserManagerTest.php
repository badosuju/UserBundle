<?php
/********************************
 * Author: M Holbrook-Bull
 * Date  : 27/02/2017
 ********************************/


use Ampisoft\UserBundle\Services\AmpUserManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder as PasswordEncoder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class AmpUserManagerTest
 * @author M Holbrook-Bull
 */
class AmpUserManagerTest extends PHPUnit_Framework_TestCase
{
    private $em;
    private $user;
    private $userRepository;
    private $groupRepository;
    private $tokenStorage;
    private $encoder;
    private $logger;
    private $eventDispatcher;
    private $manager;

    private $groupClass = 'Group';
    private $userClass = 'User';

    public function setUp()
    {
        // user class
        $this->user = $this->createMock(User::class);
        $this->user->method('getApiToken')
                   ->will($this->returnValue('abcdef12345'));

        $this->group = $this->createMock(Group::class);

        // repository
        $this->userRepository = $this->getMockBuilder(EntityRepository::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $this->userRepository->method('findOneBy')
                             ->will($this->returnValue($this->user));

        $this->groupRepository = $this->getMockBuilder(EntityRepository::class)
                                      ->disableOriginalConstructor()
                                      ->getMock();
        $this->userRepository->method('findOneBy')
                             ->will($this->returnValue($this->group));

        // token storage
        $this->tokenStorage = $this->createMock(TokenStorage::class);
        $this->tokenStorage->method('setToken')
                           ->will($this->returnValue(true));

        // entity manager
        $this->em = $this->getMockBuilder(EntityManager::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->em->method('persist')
                 ->will($this->returnValue(true));
        $this->em->method('flush')
                 ->will($this->returnValue(true));

        // argument dependent return
        $this->em->expects($this->any())
                 ->method('getRepository')
                 ->will($this->returnValueMap(
                     [
                         [$this->userClass, $this->userRepository],
                         [$this->groupClass, $this->groupRepository],
                     ]
                 ));

        // encoder
        $this->encoder = $this->createMock(PasswordEncoder::class);
        $this->encoder->method('encodePassword')
                      ->will($this->returnValue('thisisencodedhonest'));

        // logger
        $this->logger = $this->createMock(\Monolog\Logger::class);
        $this->logger->method('error')
                     ->will($this->returnValue(true));

        $this->manager = new AmpUserManager($this->em, $this->encoder, $this->tokenStorage, null, $this->userClass,
            $this->groupClass, $this->logger);
    }


    public function testCreateUser()
    {

        self::assertInstanceOf(User::class, $this->manager->loadUser('test'));
        self::assertInstanceOf(User::class, $this->manager->newUser());

        $group = $this->manager->createUserGroup('testGroup');
        self::assertInstanceOf(Group::class, $group);
        self::assertEquals('testGroup', $group->getName());



    }

    public function testUserUpdate( )
    {
        $user = new User();
        $user->setPlainPassword('test');
        $user = $this->manager->updateUser($user);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals(null, $user->getPlainPassword());
        self::assertEquals('thisisencodedhonest', $user->getPassword());
    }


}


class Group extends \Ampisoft\UserBundle\Entity\AbstractGroup
{

}

class User extends AbstractUser
{
}

