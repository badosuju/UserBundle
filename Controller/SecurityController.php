<?php
namespace Ampisoft\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;


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
