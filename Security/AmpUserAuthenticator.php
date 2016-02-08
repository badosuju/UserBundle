<?php
namespace AmpUserBundle\Security;


use GuzzleHttp\Client;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpUserAuthenticator
 * @package AmpUserBundle\Security
 */
class AmpUserAuthenticator extends AbstractGuardAuthenticator {

    /**
     * @var Router
     */
    private $router;
    /**
     * @var AmpUserProvider
     */
    private $userProvider;
    /** @var EncoderFactory */
    private $encoderFactory;
    /**
     * @var GenericOAuth1ResourceOwner
     */
    private $facebookOwner;
    /**
     * @var
     */
    private $clientId;
    /**
     * @var
     */
    private $clientSecret;


    /**
     * AmpUserAuthenticator constructor.
     * @param AmpUserProvider $userProvider
     * @param EncoderFactory $encoderFactory
     * @param Router $router
     * @param GenericOAuth2ResourceOwner $facebookOwner
     * @param $clientId
     * @param $clientSecret
     */
    public function __construct( AmpUserProvider $userProvider, EncoderFactory $encoderFactory, Router $router, GenericOAuth2ResourceOwner $facebookOwner, $clientId, $clientSecret ) {
        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->encoderFactory = $encoderFactory;
        $this->facebookOwner = $facebookOwner;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getCredentials( Request $request ) {
        if( $request->getPathInfo() != '/login' || !$request->isMethod( 'POST' ) ) {
            return null;
        }

        $data = [
            'username' => $request->request->get( 'username' ) ?? null,
            'password' => $request->request->get( 'password' ) ?? null,
            'code'     => null,
        ];

        return $data;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return \AppBundle\Entity\User|null|object
     */
    public function getUser( $credentials, UserProviderInterface $userProvider ) {
        if ( $user = $this->userProvider->loadUserByUsername( $credentials[ 'username' ] ) ) {
            return $user;
        }
        throw new CustomUserMessageAuthenticationException( 'Incorrect username or password..' );
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool|null
     */
    public function checkCredentials( $credentials, UserInterface $user ) {
        $encoder = $this->encoderFactory->getEncoder( $user );
        if ( $encoder->isPasswordValid( $user->getPassword(), $credentials[ 'password' ], $user->getSalt() ) ) {
            return true;
        }
        throw new CustomUserMessageAuthenticationException( 'Incorrect username or password' );
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ) {
        $request->getSession()
                ->set( Security::AUTHENTICATION_ERROR, $exception );
        $url = $this->router->generate( 'login' );

        return new RedirectResponse( $url );
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, $providerKey ) {
        if ( !$token->getUser()
                    ->getEnabled()
        ) {
            throw new CustomUserMessageAuthenticationException( 'Your account is disabled, please contact admin to find out why.' );
        }
        $route = $request->getBasePath();
        $url = $this->router->generate( 'homepage' );

        return new RedirectResponse( $url );
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start( Request $request, AuthenticationException $authException = null ) {
        $url = $this->router->generate( 'login' );

        return new RedirectResponse( $url );
    }

    /**
     * @return bool
     */
    public function supportsRememberMe() {
        return true;
    }


}