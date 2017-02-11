<?php
namespace Ampisoft\UserBundle\Security;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class ApiTokenAuthenticator
 * @package Ampisoft\UserBundle\Security
 */
class ApiTokenAuthenticator extends AbstractGuardAuthenticator {

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var
     */
    private $userClass;

    /**
     * ApiTokenAuthenticator constructor.
     */
    public function __construct(ObjectManager $em, $userClass) {
        $this->em = $em;
        $this->userClass = $userClass;
    }


    /**
     * @inheritDoc
     */
    public function getCredentials( Request $request ) {
        return $request->headers->get('x-token');
    }

    /**
     * @inheritDoc
     */
    public function getUser( $credentials, UserProviderInterface $userProvider ) {
        $user = $this->em->getRepository($this->userClass)
            ->findOneBy(['apiToken' => $credentials]);

        if(!$user) {



            throw new AuthenticationCredentialsNotFoundException();
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials( $credentials, UserInterface $user ) {

        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure( Request $request, AuthenticationException $exception ) {
        return new JsonResponse([
            'message' => $exception->getMessageKey(),
                    ], 401);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess( Request $request, TokenInterface $token, $providerKey ) {
        return;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe() {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function start( Request $request, AuthenticationException $authException = null ) {
        $data = $request->headers->all();
        return new JsonResponse([
            'message' => 'Authentication header required (X-TOKEN)',
            'headers' => $data
        ], 401 );
    }
    
}