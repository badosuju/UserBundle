<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/10/16
 * Time: 21:31
 */

namespace Ampisoft\UserBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\Helper\HelperInterface;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * for twig
 *
 * Class TokenManagerHelper
 * @package \Services
 */
class TokenManagerHelper extends Helper
{
    
    private $tokenManager;
    
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }
    
    public function getToken()
    {
        return $this->tokenManager->getToken();
    }
    
    public function getName()
    {
        return 'tokenManager';
    }
}