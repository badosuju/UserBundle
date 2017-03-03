<?php
/********************************
 * Author: M Holbrook-Bull
 * Date  : 03/03/2017
 ********************************/

namespace Ampisoft\UserBundle\Tests;


use Ampisoft\UserBundle\Form\Type\AdminUserFormType;
use Ampisoft\UserBundle\Form\Type\LoginType;
use Ampisoft\UserBundle\Form\Type\UserFormType;
use Symfony\Component\Form\Test\TypeTestCase;
use Ampisoft\UserBundle\Tests\Entity\TestUser as User;

/**
 * Class FormsTest
 * @package Ampisoft\UserBundle\Tests
 * @author M Holbrook-Bull
 */
class FormsTest extends TypeTestCase
{

    public function testUserType()
    {
        $formData = [
            'username'       => 'testusername',
            'email'          => 'test@email.com',
            'full_name'      => 'test testston',
            'plain_password' => 'testpassword',
        ];

        $form = $this->factory->create(UserFormType::class, null, [
            'data_class' => User::class,
        ]);

        $object = new User();
        $object->setUsername($formData['username']);
        $object->setEmail($formData['email']);
        $object->setFullName($formData['full_name']);
        $object->setPlainPassword(null);

        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    public function testLoginType()
    {
        $formData = [
            '_username'    => 'testuser',
            '_password'    => 'password',
            '_remember_me' => true,
        ];

        $form = $this->factory->create(LoginType::class);

        $form->submit($formData);

        self::assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    public function testAdminuserFormType()
    {
        $formData = [
            'username'       => 'testusername',
            'email'          => 'test@email.com',
            'full_name'      => 'test testston',
            'plain_password' => 'testpassword',
            'enabled'        => true,
            'roles'          => ['ROLE_USER'],
        ];

        $form = $this->factory->create(AdminUserFormType::class, null, [
            'data_class' => User::class,
        ]);

        $object = new User();
        $object->setUsername($formData['username']);
        $object->setEmail($formData['email']);
        $object->setFullName($formData['full_name']);
        $object->setPlainPassword(null);
        $object->setRoles(['ROLE_USER']);

        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

}
