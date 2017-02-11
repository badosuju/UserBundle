<?php

namespace Ampisoft\UserBundle\Form;

use Ampisoft\UserBundle\Security\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserForm extends UserForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add( 'roles', ChoiceType::class, [
                'choices' => UserManager::$userRolesNice,
            'multiple' => true,
            'expanded' => true,
            'required' => true
            ] )
            ->add( 'enabled', CheckboxType::class, [
                'required' => false
            ] );
    }

}
