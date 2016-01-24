<?php

namespace AmpUserBundle\Entity;


use AmpUserBundle\Source\AbstractUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ampisoft\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraint as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class User
 * @package AmpUserBundle\Entity
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="AmpUserBundle\Repository\UserRepository")
 * @Doctrine\ORM\Mapping\Table(name="user")
 *
 * @GRID\Source(columns="id, username, fullName, email, last_logged_in, roles, enabled")
 * @UniqueEntity("fullName")
 */
class User extends AbstractUser {

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $facebookID = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $googleID = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $fullName = null;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="author", cascade={"remove"})
     */
    protected $comments;

    /**
     * @ORM\Column(type="string")
     */
    protected $apiToken;

    /**
     * User constructor.
     */
    public function __construct() {
        $this->comments = new ArrayCollection();
        $seed = new \DateTime;

        $this->apiToken = openssl_digest($seed->getTimestamp() , 'sha1' );
    }

    public function setUsername( $username ) {
        if(!$this->fullName) {
            $this->fullName = $username;
        }

        $this->username = $username;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->getUsername() . '-';
    }

    public function getFullName() {
        return $this->fullName;
    }

    public function getComments() {
        return $this->comments;
    }

    public function getApiToken() {
        return $this->apiToken;
    }

    public function setApiToken( $apiToken ) {
        $this->apiToken = $apiToken;
    }

    public function setFullName( $fullName ) {
        $this->fullName = $fullName;
    }

    public function getFacebookID() {
        return $this->facebookID;
    }

    public function setFacebookID( $facebookID ) {
        $this->facebookID = $facebookID;
    }

    public function getGoogleID() {
        return $this->googleID;
    }

    public function setGoogleID( $googleID ) {
        $this->googleID = $googleID;
    }



}