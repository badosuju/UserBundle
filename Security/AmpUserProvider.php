<?php
namespace Ampisoft\UserBundle\Security;

use Ampisoft\UserBundle\Entity\AbstractUser;
use Ampisoft\UserBundle\Services\AmpUserManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpUserProvider
 * @package Ampisoft\UserBundle\Security
 */
class AmpUserProvider implements UserProviderInterface {

    /** @var AmpUserManager  */
    private $userManager;
    /**
     * @var
     */
    private $userClass;

    /**
     * AmpUserProvider constructor.
     *
     * @param AmpUserManager $userManager
     * @param $userClass
     */
    public function __construct( AmpUserManager $userManager, $userClass ) {
        $this->userManager = $userManager;
        $this->userClass = $userClass;
    }

    /**
     * @param string $username
     * @return object
     */
    public function loadUserByUsername( $username ) {
        $user = $this->userManager->loadUser( $username );
        if ( $user ) {
            return $user;
        }
        throw new UsernameNotFoundException( sprintf( 'Username "%s" does not exist.', $username ) );
    }

    /**
     * @param UserInterface $user
     * @return object
     */
    public function refreshUser( UserInterface $user ) {
        if ( !$user instanceof AbstractUser ) {
            throw new UnsupportedUserException( sprintf( 'Instances of "%s" are not supported.', get_class( $user ) ) );
        }
        return $this->loadUserByUsername( $user->getUsername() );
    }

    /**
     * @param AbstractUser $user
     */
    public function updateUser( AbstractUser $user) {
        $this->userManager->updateUser($user);
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass( $class ) {
        return $class === $this->userClass;
    }

}
