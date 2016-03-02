<?php

namespace Ampisoft\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Tests\Extension\Core\Type\RepeatedTypeTest;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class UserForm
 * @package AmpBundle\Form
 */
class UserForm extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options ) {
        $builder
            ->add( 'username', TextType::class, [ 'required' => true ] )
            ->add( 'email', TextType::class, [ 'required' => true ] )
            ->add( 'full_name', TextType::class, [ 'required' => true ] )
            ->add( 'plain_password', RepeatedType::class, [
                'type' => PasswordType::class, 'invalid_message' => 'The password fields must match.', 'options' => [
                    'attr' => [
                        'class' => 'password-field'
                    ]
                ], 'required' => false, 'first_options' => [
                    'label' => 'Password'
                ], 'second_options' => [
                    'label' => 'Repeat Password'
                ]
            ] );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver ) {
        $resolver->setDefaults( [
            'data_class' => 'Ampisoft\UserBundle\Entity\User'
        ] );
    }

}
