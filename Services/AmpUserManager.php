<?php
namespace Ampisoft\UserBundle\Services;


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

    public function __construct( EntityManager $em, UserPasswordEncoder $encoder, $userClass ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->userClass = $userClass;
    }

    /**
     * @param string $username
     * @param string $password
     * @param array $roles
     */
    public function createUser( $username = 'admin', $password = 'password', array $roles = [ 'ROLE_SUPER_ADMIN' ] ) {

        $user = new $this->userClass();
        $user->setUsername( $username )
             ->setEnabled( true )
             ->setFirstname( 'An' )
             ->setLastname( 'Admin' )
             ->setRoles( $roles )
             ->setEmail( 'admin@bigjobs.com' )
             ->setPlainPassword( $password );
        $this->updateUser( $user );
        $this->em->persist( $user );
        try {
            $this->em->flush();
        } catch( \Doctrine\DBAL\DBALException $e) {
            die('oops, an error occurred. User already exists?' . PHP_EOL);
        }
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function updateUser( UserInterface $user ) {
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