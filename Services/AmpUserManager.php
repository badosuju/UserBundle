<?php
namespace Ampisoft\UserBundle\Services;


use Ampisoft\UserBundle\Entity\AbstractGroup;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Ampisoft\UserBundle\src\Model\UserInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpUserManager
 * @package Ampisoft\UserBundle\Services
 */
class AmpUserManager {

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;
    private $userClass;
    private $groupClass;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct( EntityManager $em, UserPasswordEncoder $encoder, TokenStorage $tokenStorage, $eventDispatcher, $userClass, $groupClass ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->userClass = $userClass;
        $this->groupClass = $groupClass;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $username
     * @param string $password
     * @param array $roles
     * @return AbstractUser
     */
    public function createUser( $username, $password, $email, $groupName = null, array $roles = [ 'ROLE_ADMIN' ] ) {
        if($groupName) {
            $group = $this->em->getRepository( 'AppBundle:Group' )
                              ->findOneBy( [ 'name' => $groupName ] );

            if ( null === $group ) {
                $group = $this->createUserGroup( $groupName, $roles );
            }
        }

        /** @var AbstractUser $user */
        $user = new $this->userClass();
        $user->setUsername( $username )
             ->setEnabled( true )
             ->setFirstname( 'An' )
             ->setLastname( 'Admin' )
             ->setEmail( $email );

        if(isset($group)) {
            $user->addGroup( $group );
        } else {
            $user->setRoles($roles);
        }

        $user->setPlainPassword( $password );
        $this->updateUser( $user );
        $this->em->persist( $user );

        try {
            $this->em->flush();
        }
        catch ( \Doctrine\DBAL\DBALException $e ) {
            die( 'Oops, an error occurred. User already exists?' . PHP_EOL );
        }

        return $user;
    }

    public function newUser($groupName = 'user') {
        $group = $this->em->getRepository( 'AppBundle:Group' )
                          ->findOneBy( [ 'name' => $groupName ] );

        if ( null === $group ) {
            $group = $this->createUserGroup( $groupName, ['ROLE_USER'] );

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
    public function createUserGroup( $groupName, array $roles = [ 'ROLE_USER' ] ) {

            /** @var AbstractGroup $group */
            $group = new $this->groupClass();
            $group->setName( $groupName );
            $group->setRoles( $roles );

            $this->em->persist( $group );
            $this->em->flush();

        return $group;
    }

    /**
     * @param AbstractUser $user
     * @return AbstractUser
     */
    public function updateUser( AbstractUser $user ) {
        if ( null !== $user->getPlainPassword() ) {
            $this->encodePassword( $user );
            $this->refreshApiToken( $user );
        }
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param bool $flush
     * @return UserInterface
     */
    private function encodePassword( AbstractUser $user, $flush = false ) {
        $plainPassword = $user->getPlainPassword();
        $user->setSalt( uniqid( mt_rand(), true ) );
        $user->setPassword( $this->encoder->encodePassword( $user, $plainPassword ) )
             ->eraseCredentials();
        if ( $flush ) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param bool $flush
     * @return UserInterface
     */
    public function refreshApiToken( AbstractUser $user, $flush = false ) {
        $user->setApiToken( uniqid( mt_rand(), true ) );
        if ( $flush ) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     */
    public function loginUser(AbstractUser $user) {
        $token = new UsernamePasswordToken( $user, null, "main", $user->getRoles() );
        $this->tokenStorage
             ->setToken( $token );

    }
}