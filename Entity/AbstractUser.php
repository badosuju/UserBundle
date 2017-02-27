<?php
namespace Ampisoft\UserBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Ampisoft\UserBundle\Source\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AbstractUser
 * @package Ampisoft\UserBundle\Source
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 * @ORM\MappedSuperclass()
 */
abstract class AbstractUser implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLoggedIn;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles = [ ];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $guest = false;


    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $fullName = null;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group", inversedBy="users")
     * @var ArrayCollection
     */
    protected $groups;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Url()
     */
    protected $url = null;

    /**
     * @ORM\Column(type="string")
     */
    protected $apiToken;

    /**
     * @var
     */
    protected $googleID;

    /**
     * @var
     */
    protected $facebookID;

    /**
     * User constructor.
     */
    public function __construct() {

        $this->groups = new ArrayCollection();

        $seed = new \DateTime;
        $this->apiToken = openssl_digest( $seed->getTimestamp(), 'sha1' );
    }

    /**
     * @return mixed
     */
    public function getGoogleID()
    {
        return $this->googleID;
    }

    /**
     * @param mixed $googleID
     * @return AbstractUser
     */
    public function setGoogleID($googleID)
    {
        $this->googleID = $googleID;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFacebookID()
    {
        return $this->facebookID;
    }

    /**
     * @param mixed $facebookID
     * @return AbstractUser
     */
    public function setFacebookID($facebookID)
    {
        $this->facebookID = $facebookID;

        return $this;
    }




    /**
     * @param $username
     * @return $this
     */
    public function setUsername( $username ) {
        if ( !$this->fullName ) {
            $this->fullName = $username;
        }

        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->getUsername() . '-';
    }

    /**
     * @return null
     */
    public function getFullName() {
        return $this->fullName;
    }


    /**
     * @param $fullName
     * @return $this
     */
    public function setFullName( $fullName ) {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param $firstname
     * @return $this
     */
    public function setFirstname( $firstname ) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param $lastname
     * @return $this
     */
    public function setLastname( $lastname ) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function isGranted( $role ) {
        return in_array( $role, $this->getRoles() );
    }

    /**
     * @return null
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl( $url ) {
        $this->url = $url;

        return $this;
    }

    /**
     * @return bool
     */
    public function getGuest() {
        return $this->guest;
    }

    /**
     * @param $guest
     * @return $this
     */
    public function setGuest( $guest ) {
        $this->guest = $guest;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles() {
        $roles = (array)$this->roles;
        /** @var AbstractGroup $group */
        foreach ( $this->groups->toArray() as $group ) {
            $roles = array_merge($roles, $group->getRoles());
        }

        return array_unique( $roles );
    }

    /**
     * @param $role
     * @return $this
     */
    public function addRole( $role ) {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles( array $roles ) {
        foreach ( $roles as $role ) {
            $this->addRole( $role );
        }

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole( $role ) {
        return in_array( $role, $this->getRoles() );
    }

    /**
     * @param $role
     */
    public function removeRole( $role ) {
        unset( $this->roles[ array_search( $role, $this->roles ) ] );
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId( $id ) {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword() {
        return $this->plainPassword;
    }

    /**
     * @param $plainPassword
     * @return $this
     */
    public function setPlainPassword( $plainPassword ) {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword( $password ) {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * @param $salt
     * @return $this
     */
    public function setSalt( $salt ) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail( $email ) {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastLoggedIn() {
        return $this->lastLoggedIn;
    }

    /**
     * @param $lastLoggedIn
     * @return $this
     */
    public function setLastLoggedIn( $lastLoggedIn ) {
        $this->lastLoggedIn = $lastLoggedIn;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * @param $enabled
     * @return $this
     */
    public function setEnabled( $enabled ) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials() {
        $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getApiToken() {
        return $this->apiToken;
    }

    /**
     * @param $apiToken
     * @return $this
     */
    public function setApiToken( $apiToken ) {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups( ) {
        $groups = [];
        /** @var AbstractGroup $group */
        foreach ( $this->groups->getIterator() as $group ) {
            if($group->isActive()) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * @param AbstractGroup $group
     * @return $this
     */
    public function addGroup( AbstractGroup $group ) {
        $this->groups->add( $group );

        return $this;
    }

    /**
     * @param AbstractGroup $group
     */
    public function removeGroup( AbstractGroup $group ) {
        $this->groups->removeElement( $group );
    }
}
