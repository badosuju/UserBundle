<?php
namespace Ampisoft\UserBundle\Security;

use Ampisoft\UserBundle\Entity\AbstractUser;
use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpUserProvider
 * @package Ampisoft\UserBundle\Security
 */
class AmpUserProvider implements UserProviderInterface {

    /** @var UserManager  */
    private $userManager;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * AmpUserProvider constructor.
     *
     * @param UserManager $userManager
     * @param TokenStorage $tokenStorage
     */
    public function __construct( UserManager $userManager, TokenStorage $tokenStorage ) {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $username
     * @return User|null|object
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
     * @return User|null|object|UserInterface
     */
    public function refreshUser( UserInterface $user ) {
        if ( !$user instanceof User ) {
            throw new UnsupportedUserException( sprintf( 'Instances of "%s" are not supported.', get_class( $user ) ) );
        }
        return $this->loadUserByUsername( $user->getUsername() );
    }

    public function updateUser( AbstractUser $user) {
        $this->userManager->updateUser($user);
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass( $class ) {
        return $class === 'AppBundle\Entity\User';
    }

}