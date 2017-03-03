<?php

namespace Ampisoft\UserBundle\Form\Type;

use Ampisoft\UserBundle\Entity\AbstractUser;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class UserForm
 * @package AmpBundle\Form
 */
class UserFormType extends AbstractType {

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
                'type' => PasswordType::class,
                'options' => [
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
            'data_class' => AbstractUser::class
        ] );
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'amp_user_form';
    }

}
