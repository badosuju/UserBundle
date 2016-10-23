<?php
namespace Ampisoft\UserBundle\Security;


use Ampisoft\UserBundle\Services\AmpUserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class FormLoginAuthenticator
 * @package Ampisoft\UserBundle\Security
 */
class FormLoginAuthenticator extends AbstractGuardAuthenticator {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var UserPasswordEncoder
     */
    private $userPasswordEncoder;
    /**
     * @var
     */
    private $loginPath;
    /**
     * @var AmpUserManager
     */
    private $userManager;

    public function __construct( Router $router, UserPasswordEncoder $userPasswordEncoder, $loginPath, AmpUserManager $userManager, $successPath ) {
        $this->router = $router;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->loginPath = $loginPath;
        $this->userManager = $userManager;
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl() {
        return $this->router->generate( $this->loginPath );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultSuccessRedirectUrl() {
        return $this->router->generate( $this->getLoginUrl());
    }

    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ) {
        return parent::onAuthenticationFailure( $request, $exception );
    }


    /**
     * @inheritDoc
     */
    public function getCredentials( Request $request ) {
        if ( $request->getPathInfo() !== '/login_check' ) {
            return;
        }
        $data = $request->request->get( 'login' );

        $request->getSession()
                ->set( Security::LAST_USERNAME, $data['_username'] );

        return [
            'username' => $data['_username'],
            'password' => $data['_password'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser( $credentials, UserProviderInterface $userProvider ) {
        $user = $userProvider->loadUserByUsername( $credentials[ 'username' ] );

        return $user;
    }

    public function onAuthenticationSuccess( Request $request, TokenInterface $token, $providerKey ) {
        $user = $token->getUser();
        $user->setLastLoggedIn( new \DateTime() );
        $this->userManager->updateUser( $user );
        
        return new RedirectResponse($this->router->generate('homepage'));
    }


    /**
     * @inheritDoc
     */
    public function checkCredentials( $credentials, UserInterface $user ) {
        $plainPassword = $credentials[ 'password' ];
        $encoder = $this->userPasswordEncoder;
        if ( !$encoder->isPasswordValid( $user, $plainPassword ) ) {
            throw new BadCredentialsException();
        }

        return true;
    }
    
    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        
        return new RedirectResponse($this->loginPath);
    }
    
    /**
     * Does this method support remember me cookies?
     *
     * Remember me cookie will be set if *all* of the following are met:
     *  A) This method returns true
     *  B) The remember_me key under your firewall is configured
     *  C) The "remember me" functionality is activated. This is usually
     *      done by having a _remember_me checkbox in your form, but
     *      can be configured by the "always_remember_me" and "remember_me_parameter"
     *      parameters under the "remember_me" firewall key
     *
     * @return bool
     */
    public function supportsRememberMe()
    {
        return true;
    }
    
    
}