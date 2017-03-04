<?php

/********************************
 * Author: M Holbrook-Bull
 * Date  : 01/03/2017
 ********************************/

namespace Ampisoft\UserBundle\Tests;

use Ampisoft\UserBundle\Entity\AbstractGroup;
use Ampisoft\UserBundle\Entity\AbstractUser;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class AbstractUserTest extends TestCase
{


    public function testSettersAndGetters()
    {


        $user = new TestUser();

        $reflection = new \ReflectionClass(TestUser::class);

        $setters = array_filter($reflection->getMethods(), function ($method) {
            return strpos($method, 'set') !== false;
        });

        /** @var \ReflectionMethod $method */
        foreach ($setters as $method) {
            $setName = $method->getName();
            $getName = str_replace('set', 'get', $method->getName());

            switch ($setName) {
                case 'setGroups':
                    continue 2;

                    break;
                default:
                    $params = $method->getParameters();
                    if ($params[0]->isArray()) {
                        $data = ['test'];
                        $array = true;
                    } else {
                        $data = 'test';
                        $array = false;
                    }
            }

            $user->$setName($data);
            self::assertEquals($array ? ['test'] : 'test', $user->$getName());
        }


    }

    public function testHasMethods()
    {

        $user = new TestUser();

        $reflection = new \ReflectionClass(TestUser::class);

        $has = array_filter($reflection->getMethods(), function ($method) {
            return strpos($method, 'has') !== false;
        });

        foreach ($has as $method) {
            $data = null;

            switch ($method->getName()) {
                case 'hasRole':
                    $data = 'ROLE_ADMIN';
                    continue;
                    break;
            }
            $m = $method->getName();
            self::assertFalse($user->$m($data));
        }
    }


}


class TestUser extends AbstractUser
{

}

class TestGroup extends AbstractGroup
{

}