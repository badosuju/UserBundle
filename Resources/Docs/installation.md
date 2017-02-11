###Installation

```
$ composer require ampisoft/userbundle
```

#####AppKernel.php
```php
 // ...
 $bundles = array(
    //...
    new AmpUserBundle\AmpUserBundle(),
    
    //...
 );
 //...
```

#####routing.yml
```yml
amp_user:
    resource: "@AmpUserBundle/Resources/config/routing.yml"
```


#####security.yml
```yml
main:
            anonymous: ~

            guard:
                authenticators:
                    - amp_user.authenticator

            oauth:
                resource_owners:
                    facebook:           "/login/check-facebook"
                login_path:        /connect/facebook
                check_path:        /connect_check
                default_target_path: /
                failure_path:      /login
                oauth_user_provider:
                    service: amp_user.provider.oauth
```