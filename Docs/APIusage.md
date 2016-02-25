Using the API authentication
============================


##Setup

In your security.yml file, you will need to define a firewall for your api.  For this to work, you need to prefix any api endpoint with `/api`.

###Security.yml

```yml
        api:
            pattern: ^/api
            anonymous: ~
            stateless: true
            guard:
                authenticators:
                    - amp_user.api_token_authenticator

```

###Services.yml

```yml
imports:
    - { resource: '@AmpisoftUserbundle/Resources/config/services.yml' }
```

###Config.yml 
***(omit to use defaults)***

```yml
ampisoft_userbundle:
    classes:
        user: AppBundle\Entity\User
        group: AppBundle\Entity\Group
```

##Usage

The authenticator checks for the existence of a header key `x-token`.  It will then search the user database for a user with a matching `apiToken`.  If you use the setup
above, then the session will be stateless, and will reauthenticate on every request.  If you don't want this, simply remove the `stateless` entry from the firewall setup.

##Client side

**(jQuery required)**

To automatically set the header to the correct token value, paste this into your `base.html.twig` template. 

```javascript
<script>
    $(function () {
        var token = 'xxx'; // get the token for your user in whatever way you do.
        $.ajaxSetup({
            cache: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('x-token', token);
            }
        });
    });
</script>
```