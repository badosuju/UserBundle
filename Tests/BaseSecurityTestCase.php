<?php
/********************************
 * Author: M Holbrook-Bull
 * Date  : 01/03/2017
 ********************************/

namespace Ampisoft\UserBundle\Tests;


use Ampisoft\UserBundle\Entity\AbstractGroup;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Ampisoft\UserBundle\Services\AmpUserManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder as PasswordEncoder;

class BaseSecurityTestCase extends TestCase
{

    protected $em;
    /** @var  AbstractUser */
    protected $user;
    /** @var  AbstractGroup */
    protected $group;
    protected $userRepository;
    protected $groupRepository;
    protected $tokenStorage;
    protected $encoder;
    protected $logger;
    protected $eventDispatcher;
    /** @var  AmpUserManager */
    protected $manager;

    protected $groupClass = 'Group';
    protected $userClass = 'User';
    
    public function setUp()
    {
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
        $this->tokenStorage = new TokenStorage();

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
    
}