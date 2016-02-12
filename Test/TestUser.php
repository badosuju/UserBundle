<?php
namespace Ampisoft\UserBundle\Test;


use Ampisoft\UserBundle\Entity\AbstractUser;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class TestUser
 * @package Ampisoft\UserBundle\Test
 */
class TestUser extends AbstractUser {
    /**
     * @inheritDoc
     */
    public function serialize() {
        // TODO: Implement serialize() method.
    }

    /**
     * @inheritDoc
     */
    public function unserialize( $serialized ) {
        // TODO: Implement unserialize() method.
    }

    public function toArray() {
        // TODO: Implement toArray() method.
    }
    
    
}