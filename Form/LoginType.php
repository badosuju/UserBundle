<?php

namespace Ampisoft\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Router;


class LoginType extends AbstractType {

    public function buildForm( FormBuilderInterface $builder, array $options ) {

        $builder->setAction( $options[ 'action' ] )
                ->add( '_username', TextType::class, [
                    'label' => 'Username',
                    'attr'  => [
                        'placeholder' => 'Username',
                    ],
                    'data'  => $options[ 'last_username' ],
                ] )
                ->add( '_password', PasswordType::class, [
                    'label' => 'Password',
                    'attr'  => [
                        'placeholder' => 'Password',
                    ],
                ] )
                ->add( '_remember_me', CheckboxType::class, [
                    'label' => 'Remember me',
                ] )
                ->add( 'submit', SubmitType::class );

    }

    public function configureOptions( OptionsResolver $resolver ) {
        $resolver->setDefaults( [
                                    'action'        => null,
                                    'last_username' => null,
                                ] );
    }

}
