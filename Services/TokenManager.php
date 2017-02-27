<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/10/16
 * Time: 21:30
 */
namespace Ampisoft\UserBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class TokenManager
 * @package AppBundle\Resources\Services
 */
class TokenManager
{

    /**
     * @var Session
     */
    private $session;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * TokenManager constructor.
     * @param Session $session
     * @param TokenStorage $tokenStorage
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(Session $session, TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker)
    {
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->session->getId();
        }
        
        return $this->tokenStorage->getToken()
                                  ->getUser()
                                  ->getApiToken();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function checkToken(Request $request)
    {
        if (null === $request->query->get('_token')) {
            return false;
        }
        // guest (wont be able to do much)
        $token = $request->query->get('_token');
        if ($this->session->getId() === $token) {
            return true;
        }
        // fuller access
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') && $this->tokenStorage->getToken()
                                                                                                   ->getUser()
                                                                                                   ->getApiToken() === $token
        ) {
            return true;
        }
        
        return false;
    }
    
}
