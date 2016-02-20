<?php

namespace Ampisoft\UserBundle\Services;


use Ampisoft\UserBundle\Form\LoginType;
use Symfony\Component\Form\FormFactory;


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
     * FormManager constructor.
     */
    public function __construct(FormFactory $formFactory) {

        $this->formFactory = $formFactory;
    }

    public function getLoginForm($data = null, array $options = []) {
        return $this->formFactory->create(LoginType::class, $data, $options);
    }
}