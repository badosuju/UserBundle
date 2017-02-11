<?php
namespace Ampisoft\UserBundle\Controller;


use Ampisoft\UserBundle\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class SecurityController
 * @package Ampisoft\UserBundle\Controller
 */
class SecurityController extends Controller {

    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction() {
        $helper = $this->get( 'security.authentication_utils' );
        $form = $this->get( 'amp_security.form_manager' )
                     ->getLoginForm();

        return new RedirectResponse($this->generateUrl($this->getParameter('amp_userbundle.login_path')));
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction() {
        // will never run
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction() {
        // will never run
    }
}
