<?php
namespace AmpUserBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class UserManager
 * @package AppBundle\Services
 */
class UserManager {

    public static $userRoles = [
        'ROLE_ADMIN',
        'ROLE_TESTER',
        'ROLE_USER'
    ];

    public static $userRolesNice = [
        'ROLE_ADMIN' => 'Admin',
        'ROLE_OAUTH_USER' => 'Social media user',
        'ROLE_TESTER' => 'Tester',
        'ROLE_USER' => 'User'
    ];

    private $em;
    /** @var EncoderFactory  */
    private $encoderFactory;
    /**
     * @var Session
     */
    private $session;

    /**
     * UserManager constructor
     *
     * @param EntityManager $em
     * @param EncoderFactory $encoderFactory
     * @param Session $session
     */
    public function __construct( EntityManager $em, EncoderFactory $encoderFactory, Session $session ) {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->session = $session;
    }

    public function loadUser( $username, $key = 'username' ) {
        $user = $this->em->getRepository( 'AppBundle:User' )->findOneBy( [ $key => $username ] );
        return $user;
    }

    /**
     * Updates the users details including re-encrypting their password
     * @param User $user
     */
    public function updateUser( User $user ) {
        $this->encodePassword( $user );
        $user->eraseCredentials();

        $this->em->persist( $user );
        $this->em->flush();
    }

    /**
     * Creates a new user
     * @param $username
     * @param $password
     * @param $email
     * @param array $roles
     * @param bool $flush
     * @return User
     */
    public function createUser( $username, $password, $email, $roles = [ 'ROLE_USER' ], $flush = true ) {
        if ( !is_array( $roles ) ) {
            $roles = (array) $roles;
        }

        $user = new User();
        $user->setEnabled( true );
        $user->setPlainPassword( $password );
        $user->setUsername( $username );
        $user->setEmail( $email );
        $user->setRoles( $roles );
        $this->encodePassword( $user );

        if($flush) {
            $this->em->persist( $user );
            $this->em->flush();
        }

        return $user;
    }

    /**
     * Remove the user from the database
     * @param User $user
     */
    public function removeUser( User $user ) {
        $this->em->remove( $user );
    }

    /**
     * @param User $user
     * @return User
     */
    private function encodePassword( User $user ) {
        $plainPassword = $user->getPlainPassword();
        $encoder = $this->encoderFactory->getEncoder($user);

        if ( strlen( $plainPassword ) > 0 ) {
            $salt = openssl_digest( $plainPassword, 'sha1' );
            $user->setSalt( $salt);
            $encoded = $encoder->encodePassword( $plainPassword, $salt );
            $user->setPassword( $encoded );
            $user->eraseCredentials(); // strip off the plain password
        }
        return $user;
    }

}