<?php
namespace Ampisoft\UserBundle\Services;


use Ampisoft\UserBundle\Entity\AbstractGroup;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Ampisoft\UserBundle\src\Model\UserInterface;
use DBALException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;


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

    public function __construct( EntityManager $em, UserPasswordEncoder $encoder, $userClass, $groupClass ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->userClass = $userClass;
        $this->groupClass = $groupClass;
    }

    /**
     * @param string $username
     * @param string $password
     * @param array $roles
     * @return AbstractUser
     */
    public function createUser( $username = 'admin', $password = 'password', array $roles = [ 'ROLE_SUPER_ADMIN' ] ) {
        $group = $this->em->getRepository( 'AppBundle:Group' )
                          ->findOneBy( [ 'name' => 'admin' ] );

        if(null === $group) {
            $group = $this->createUserGroup( 'admin', $roles );
        }
        /** @var AbstractUser $user */
        $user = new $this->userClass();
        $user->setUsername( $username )
             ->setEnabled( true )
             ->setFirstname( 'An' )
             ->setLastname( 'Admin' )
             ->setEmail( 'admin@bigjobs.com' )
             ->addGroup( $group )
             ->setPlainPassword( $password );
        $this->updateUser( $user );
        $this->em->persist( $user );
        try {
            $this->em->flush();
        }
        catch ( \Doctrine\DBAL\DBALException $e ) {
            die( 'oops, an error occurred. User already exists?' . PHP_EOL );
        }

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
        $this->em->flush();

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param bool $flush
     * @return UserInterface
     */
    private function encodePassword( UserInterface $user, $flush = false ) {
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
    public function refreshApiToken( UserInterface $user, $flush = false ) {
        $user->setApiToken( uniqid( mt_rand(), true ) );
        if ( $flush ) {
            $this->em->flush();
        }

        return $user;
    }
}