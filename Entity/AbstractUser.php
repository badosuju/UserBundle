<?php
namespace Ampisoft\UserBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ampisoft\UserBundle\src\Model\UserInterface;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class User
 * @package Ampisoft\UserBundle\Entity
 */
abstract class AbstractUser implements UserInterface, \Serializable {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $salt;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $apiToken;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $locked = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $expired = false;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group", inversedBy="users")
     * @var ArrayCollection
     */
    protected $groups;

    /**
     * AbstractUser constructor.
     */
    public function __construct( ) {
        $this->groups = new ArrayCollection();
    }


    public function getId() {
        return $this->id;
    }

    public function setId( $id ) {
        $this->id = $id;

        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername( $username ) {
        $this->username = $username;

        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail( $email ) {
        $this->email = $email;

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

    public function getSalt() {
        return $this->salt;
    }

    public function setSalt( $salt ) {
        $this->salt = $salt;

        return $this;
    }

    public function addRole( $role ) {
        $this->roles[] = $role;
    }

    public function setRoles( array $roles ) {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        if($this->groups) {
            $roles = [];
            foreach ( $this->groups as $group ) {
                $roles = array_merge($roles, $group->getGroups());
            }
            return array_unique($roles);
        }
        return $this->roles;
    }

    public function getApiToken() {
        return $this->apiToken;
    }

    public function setApiToken( $apiToken ) {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        $this->plainPassword = null;
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonExpired() {
        return !$this->expired;
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonLocked() {
        return !$this->locked;
    }

    /**
     * @inheritDoc
     */
    public function isCredentialsNonExpired() {
        return !$this->expired;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled() {
        return $this->isActive;
    }

    public function setEnabled($enabled) {
        $this->isActive = $enabled;

        return $this;
    }

    /**
     * @see \Serializable::serialize()
     * @return mixed
     */
    abstract public function serialize();

    /**
     * @see \Serializable::unserialize()
     * @param string $serialized
     * @return mixed
     */
    abstract public function unserialize( $serialized );

    abstract public function toArray();
}