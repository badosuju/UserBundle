<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/10/16
 * Time: 21:31
 */

namespace Ampisoft\UserBundle\Services;

use Symfony\Component\Templating\Helper\Helper;


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

    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * TokenManagerHelper constructor.
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->tokenManager->getToken();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tokenManager';
    }
}
