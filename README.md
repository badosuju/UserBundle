Ampisoft User Bundle
===================================
| Branch | Status |
|:---|:-----------|
| Master | [![Build Status](https://secure.travis-ci.org/Ampisoft/UserBundle.png?branch=master)](http://travis-ci.org/Ampisoft/UserBundle) |

Provides a clean and simple solution for those with low-end user bundle needs.

Test

##Features

- Form based authentication
- Api token based authentication
- User base class
- Group base class (use or don't use, up to you)

##What it doesn't do..

- registration
- profile pages
- lots of bloaty stuff you dont need

#Installation

Using composer:
```bash
$ composer.phar require ampisoft/user-bundle
```

Register in AppKernel.php
```php
    public function registerBundles()
    {
        $bundles = array(
        
        // .....
            new Ampisoft\UserBundle\AmpUserBundle(),
        // .....

        );
    }    
```

###Setup your user entity

***(alter tables names etc as you need)***

```php

use MyApp\UserBundle\Entity\AbstractUser as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="id_UNIQUE", columns={"id"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 *
 */
class User extends BaseUser  {

    public function __construct() {
        parent::__construct();
        
        // add to the constructor if needed.
    }
    
    // whatever other stuff you want in here
    
}

```
you need to be extending the mapped superclasses

```php
use Ampisoft\UserBundle\Entity\AbstractGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="access_group" )
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\GroupRepository" )
 */
class Group extends AbstractGroup {
    public function __construct() {
        parent::__construct();
        
        // add to the constructor if needed.
    }
    // whatever else you want in here.
}
```

> The User/Group entities have a manyToMany relationship built in (pointing at AppBundle\Entity\\(Group | User).  If you want to override this just define the protected members again.

#Configuration

>For API authentication specifics, please see the seperate [setup](Docs/APIusage.md) document.

###Security.yml

```yml
    providers:
        amp_user_bundle:
            entity:
                class: AppBundle:User
                property: username

    firewalls:
        api:
            pattern: ^/api
            anonymous: ~
            stateless: true
            guard:
                authenticators:
                    - amp_user.api_token_authenticator

        main:
            anonymous: ~
            logout: ~
            guard:
              authenticators:
                - amp_user.form_login_authenticator
```

###Routing.yml

```yml
amp_user:
    resource: "@AmpUserBundle/Controller"
    type:     annotation

```

###Services.yml

```yml
imports:
    - { resource: '@AmpUserBundle/Resources/config/services.yml' }
```

###Config.yml 
***(omit to use defaults)***

```yml
amp_user:
    classes:
        user: AppBundle\Entity\User
        group: AppBundle\Entity\Group
    templates:
        login: 'AmpUserBundle:security:login.html.twig'
    paths:
        login: 'index'    
```
- templates: use this to override the login template
- paths > login: this is the route name that will be used to redirect the user in the event of failed login, or unauthorised access

##Creating your first user

Simply run this command from terminal:

```bash
$ bin/console amp:user:create username password email
```

This will create an admin user as follows:

| Username | Password | Roles |
|:--|:--|:--
| username | password | ROLE_SUPER_ADMIN |
