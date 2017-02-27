<?php
namespace Ampisoft\UserBundle\Entity;


use Ampisoft\UserBundle\Source\Traits\GetSafeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class Group
 * @package Ampisoft\UserBundle\Entity
 * @ORM\MappedSuperclass()
 */
abstract class AbstractGroup {

    use GetSafeTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles = [ ];

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active = true;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="groups")
     */
    protected $users;

    /**
     * AbstractGroup constructor.
     */
    public function __construct() {
        $this->users = new ArrayCollection();
    }

    /**
     * @param $id
     */
    public function setId( $id )
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName( $name ) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param string|array $role
     * @return $this
     */
    public function addRole( $role ) {
        if(!is_array($role)) {
            $role = (array)$role;
        }
        foreach ( $role as $r ) {
            $this->roles[] = strtoupper( $r );
        }

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function removeRole( $role ) {
        $index = array_search( strtoupper( $role ), $this->roles );
        if ( $index ) {
            unset( $this->roles[ $index ] );

            return true;
        }

        return false;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles( array $roles ) {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->active;
    }

    /**
     * @param $active
     * @return $this
     */
    public function setActive( $active ) {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }
    

    
}
