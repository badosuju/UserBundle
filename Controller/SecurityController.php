<?php
namespace Ampisoft\UserBundle\Controller;


use Ampisoft\UserBundle\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class SecurityController extends Controller {

    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction() {
        $helper = $this->get( 'security.authentication_utils' );
        $form = $this->get( 'amp_security.form_manager' )
                     ->getLoginForm();

        return $this->render( $this->getParameter( 'ampisoft_userbundle.templates.login' ), [
            'form'  => $form->createView(),
            'error' => $helper->getLastAuthenticationError(),
        ] );
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
