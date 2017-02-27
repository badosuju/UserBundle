<?php
namespace Ampisoft\UserBundle\Services;


use Ampisoft\UserBundle\Entity\AbstractGroup;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpUserManager
 * @package Ampisoft\UserBundle\Services
 */
class AmpUserManager
{

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;
    /**
     * @var
     */
    private $userClass;
    /**
     * @var
     */
    private $groupClass;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * AmpUserManager constructor.
     * @param EntityManager $em
     * @param UserPasswordEncoder $encoder
     * @param TokenStorage $tokenStorage
     * @param $eventDispatcher
     * @param $userClass
     * @param $groupClass
     * @param Logger $logger
     */
    public function __construct(
        EntityManager $em,
        UserPasswordEncoder $encoder,
        TokenStorage $tokenStorage,
        $eventDispatcher,
        $userClass,
        $groupClass,
        Logger $logger
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->userClass = $userClass;
        $this->groupClass = $groupClass;
        $this->logger = $logger;
    }

    /**
     * @param $username
     * @return null|object
     */
    public function loadUser($username)
    {
        return $this->em->getRepository($this->userClass)->findOneBy(['username' => $username]);
    }

    /**
     * @param string $username
     * @param string $password
     * @param $email
     * @param null $groupName
     * @param array $roles
     * @return AbstractUser
     * @throws \Exception
     */
    public function createUser($username, $password, $email, $groupName = null, array $roles = ['ROLE_ADMIN'])
    {
        if ($groupName) {
            $group = $this->em->getRepository($this->groupClass)
                              ->findOneBy(['name' => $groupName]);

            if (null === $group) {
                $group = $this->createUserGroup($groupName, $roles);
            }
        }

        /** @var AbstractUser $user */
        $user = new $this->userClass();
        $user->setUsername($username)
             ->setEnabled(true)
             ->setFirstname('An')
             ->setLastname('Admin')
             ->setEmail($email);

        if (isset($group)) {
            $user->addGroup($group);
        } else {
            $user->setRoles($roles);
        }

        $user->setPlainPassword($password);
        $this->updateUser($user);
        $this->em->persist($user);

        try {
            $this->em->flush();
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->logger->error('Dbal exception: ' . $e);
            throw new \Exception($e . PHP_EOL);
        }

        return $user;
    }

    /**
     * @param string $groupName
     * @return AbstractUser
     */
    public function newUser($groupName = 'user')
    {
        $group = $this->em->getRepository($this->groupClass)
                          ->findOneBy(['name' => $groupName]);

        if (null === $group) {
            $group = $this->createUserGroup($groupName, ['ROLE_USER']);

        }

        /** @var AbstractUser $user */
        $user = new $this->userClass();
        $user->addGroup($group);

        return $user;
    }

    /**
     * @param $groupName
     * @param array $roles
     * @return AbstractGroup
     */
    public function createUserGroup($groupName, array $roles = ['ROLE_USER'])
    {

        /** @var AbstractGroup $group */
        $group = new $this->groupClass();
        $group->setName($groupName);
        $group->setRoles($roles);

        $this->em->persist($group);
        $this->em->flush();

        return $group;
    }

    /**
     * @param AbstractUser $user
     * @return AbstractUser
     */
    public function updateUser(AbstractUser $user)
    {
        if (null !== $user->getPlainPassword()) {
            $this->encodePassword($user);
            $this->refreshApiToken($user);
        }
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param AbstractUser $user
     * @param bool $flush
     * @return AbstractUser
     */
    private function encodePassword(AbstractUser $user, $flush = false)
    {
        $plainPassword = $user->getPlainPassword();
        $user->setSalt(uniqid(mt_rand(), true));
        $user->setPassword($this->encoder->encodePassword($user, $plainPassword))
             ->eraseCredentials();
        if ($flush) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param AbstractUser $user
     * @param bool $flush
     * @return AbstractUser
     */
    public function refreshApiToken(AbstractUser $user, $flush = false)
    {
        $user->setApiToken(uniqid(mt_rand(), true));
        if ($flush) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param AbstractUser $user
     */
    public function loginUser(AbstractUser $user)
    {
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->tokenStorage
            ->setToken($token);

    }
}

