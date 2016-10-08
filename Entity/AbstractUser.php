<?php
namespace Ampisoft\UserBundle\Entity;


use Ampisoft\UserBundle\Source\Traits\GetSafeTrait;
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

    use GetSafeTrait;

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
     * @ORM\Column(type="string", length=50, nullable=false)
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
     * User constructor.
     */
    public function __construct() {

        $this->groups = new ArrayCollection();

        $seed = new \DateTime;
        $this->apiToken = openssl_digest( $seed->getTimestamp(), 'sha1' );
    }

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

    public function getFullName() {
        return $this->fullName;
    }


    public function setFullName( $fullName ) {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function setFirstname( $firstname ) {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setLastname( $lastname ) {
        $this->lastname = $lastname;

        return $this;
    }

    public function isGranted( $role ) {
        return in_array( $role, $this->getRoles() );
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl( $url ) {
        $this->url = $url;

        return $this;
    }

    public function getGuest() {
        return $this->guest;
    }

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

    public function addRole( $role ) {
        $this->roles[] = $role;

        return $this;
    }

    public function setRoles( array $roles ) {
        foreach ( $roles as $role ) {
            $this->addRole( $role );
        }

        return $this;
    }

    public function hasRole( $role ) {
        return in_array( $role, $this->getRoles() );
    }

    public function removeRole( $role ) {
        unset( $this->roles[ array_search( $role, $this->roles ) ] );
    }

    public function getId() {
        return $this->id;
    }

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

    public function getPlainPassword() {
        return $this->plainPassword;
    }

    public function setPlainPassword( $plainPassword ) {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

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

    public function setSalt( $salt ) {
        $this->salt = $salt;

        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail( $email ) {
        $this->email = $email;

        return $this;
    }

    public function getLastLoggedIn() {
        return $this->lastLoggedIn;
    }

    public function setLastLoggedIn( $lastLoggedIn ) {
        $this->lastLoggedIn = $lastLoggedIn;

        return $this;
    }

    public function getEnabled() {
        return $this->enabled;
    }

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

    public function getApiToken() {
        return $this->apiToken;
    }

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