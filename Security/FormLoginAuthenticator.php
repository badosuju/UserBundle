<?php
namespace Ampisoft\UserBundle\Security;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class FormLoginAuthenticator
 * @package Ampisoft\UserBundle\Security
 */
class FormLoginAuthenticator extends AbstractFormLoginAuthenticator {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var UserPasswordEncoder
     */
    private $userPasswordEncoder;

    public function __construct( Router $router, UserPasswordEncoder $userPasswordEncoder ) {
        $this->router = $router;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl() {
        return $this->router->generate( 'security_login' );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultSuccessRedirectUrl() {
        return $this->router->generate( 'homepage' );
    }

    /**
     * @inheritDoc
     */
    public function getCredentials( Request $request ) {
        if ( $request->getPathInfo() != '/login_check' ) {
            return;
        }
        $username = $request->request->get( '_username' );
        $request->getSession()
                ->set( Security::LAST_USERNAME, $username );
        $password = $request->request->get( '_password' );

        return [
            'username' => $username,
            'password' => $password,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser( $credentials, UserProviderInterface $userProvider ) {
        $user = $userProvider->loadUserByUsername( $credentials[ 'username' ] );

        return $user;
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
}