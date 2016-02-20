<?php

namespace Ampisoft\UserBundle\Services;


use Ampisoft\UserBundle\Form\LoginType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class FormFactory
 * @package Ampisoft\UserBundle\Services
 */
class FormManager {

    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var Router
     */
    private $router;

    /**
     * FormManager constructor.
     */
    public function __construct( FormFactory $formFactory, AuthenticationUtils $authenticationUtils, Router $router ) {

        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
        $this->router = $router;
    }

    /**
     * @param null $data
     * @param array $options
     * @return \Symfony\Component\Form\Form
     */
    public function getLoginForm( $data = null, array $options = [ ] ) {
        $options[ 'last_username' ] = $this->authenticationUtils->getLastUsername();

        if ( !array_key_exists( 'action', $options ) ) {
            $options[ 'action' ] = $this->router->generate( 'security_login_check' );
        }

        return $this->formFactory->create( LoginType::class, $data, $options );
    }
}