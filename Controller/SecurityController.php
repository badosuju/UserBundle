<?php

namespace AmpUserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class SecurityController
 * @package AmpUserBundle\Controller
 */
class SecurityController extends Controller {

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get( 'security.authentication_utils' );
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render( 'security/login.html.twig', array(
            'last_username' => $lastUsername, 'error' => $error,
        ) );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request) {
        $this->get( 'security.token_storage' )->setToken( null );
        $request->getSession()->invalidate();

        return new RedirectResponse($this->generateUrl('homepage'));
    }
}
