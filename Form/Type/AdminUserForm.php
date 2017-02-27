<?php

namespace Ampisoft\UserBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AdminUserForm
 * @package Ampisoft\UserBundle\Form
 * @author M Holbrook-Bull
 */
class AdminUserForm extends UserForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add( 'roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER',
                    'ROLE_ADMIN',
                    'ROLE_SUPER_ADMIN'
                ],
            'multiple' => true,
            'expanded' => true,
            'required' => true
            ] )
            ->add( 'enabled', CheckboxType::class, [
                'required' => false
            ] );
    }


}
