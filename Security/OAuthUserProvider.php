<?php
/**
 * Author: Matt Holbrook-Bull <matt@ampisoft.com>
 * Date: 30/10/16
 * Time: 16:19
 */

namespace Ampisoft\UserBundle\Security;

use Ampisoft\UserBundle\Services\AmpUserManager;
use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Validator\RecursiveValidator;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class OAuthUserProvider
 * @package AmpUserBundle\Security
 */
class OAuthUserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{
    
    /**
     * @var UserManager
     */
    private $userManager;
    
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    
    /**
     * @var array
     */
    private $args;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Validator
     */
    private $validator;
    
    /**
     * OAuthUserProvider constructor.
     * @param AmpUserManager $userManager
     * @param TokenStorage $tokenStorage
     * @param array $args
     * @param Session $session
     * @param Validator $validator
     */
    public function __construct( AmpUserManager $userManager, TokenStorage $tokenStorage, $args = [], Session $session, $validator )
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
        $this->args = $args;
        $this->session = $session;
        $this->validator = $validator;
    }
    
    
    public function loadUserByUsername( $username )
    {
        $user = $this->userManager->loadUser( $username );
        if ( $user ) {
            return $user;
        }
        throw new UsernameNotFoundException( sprintf( 'Username "%s" does not exist.', $username ) );
    }
    
    public function refreshUser( UserInterface $user )
    {
        if ( !$user instanceof User ) {
            throw new UnsupportedUserException( sprintf( 'Instances of "%s" are not supported.', get_class( $user ) ) );
        }
        
        return $this->loadUserByUsername( $user->getUsername() );
    }
    
    public function supportsClass( $class )
    {
        return $class === 'AppBundle\Entity\User';
    }
    
    public function loadUserByOAuthUserResponse( UserResponseInterface $response )
    {
        $socialID = $response->getUsername();
        /** @var User $user */
        $user = $this->userManager->loadUser( [ 'facebookId' => $socialID ] );
        $update = true;
        $email = $response->getEmail();
        //check if the user already has the corresponding social account
        if ( null === $user ) {
            //check if the user has a normal account
            $user = $this->userManager->loadUser( $email, 'email' );
            if ( null === $user || !$user instanceof UserInterface ) {
                //if the user does not have a normal account, set it up:
                /** @var User $user */
                $name = $response->getNickname() ?? $response->getRealName();
                $user = $this->userManager->createUser( $name, md5( uniqid() ), $response->getEmail(), [ 'ROLE_OAUTH_USER' ] );
                $user->setEmail( $email );
                $user->setFullName( $name );
                $user->setEnabled( true );
                $violations = $this->validator->validate( $user );
                $update = !$violations->count() === 0;
                if ( $violations->count() === 0 ) {
                    $this->session->getFlashBag()
                                  ->add( 'warning', 'Welcome! You must complete your profile in order to use the features on the site.' );
                } else {
                    throw new CustomUserMessageAuthenticationException( 'An account in your name already exists.' );
                }
            }
            if ( $update ) {
                //then set its corresponding social id
                $service = $response->getResourceOwner()
                                    ->getName();
                switch ( $service ) {
                    case 'google':
                        $user->setGoogleID( $socialID );
                        break;
                    case 'facebook':
                        $user->setFacebookID( $socialID );
                        break;
                }
                $this->userManager->updateUser( $user );
            }
            
        } else {
            //and then login the user
            $token = new UsernamePasswordToken( $user, null, 'main', $user->getRoles() );
            $this->tokenStorage->setToken( $token );
        }
        $user->setLastLoggedIn( new \DateTime() );
        $this->userManager->updateUser( $user );
        
        return $user;
    }
    
    
}