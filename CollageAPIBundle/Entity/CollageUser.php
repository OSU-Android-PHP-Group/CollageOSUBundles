<?php

namespace OSU\CollageAPIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OSU\CollageAPIBundle\Entity\CollageUser
 */
class CollageUser
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $user_name
     */
    private $user_name;

    /**
     * @var string $password_hash
     */
    private $password_hash;

    /**
     */
    private $images;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user_name
     *
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->user_name = $userName;
    }

    /**
     * Get user_name
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Set password_hash
     *
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->password_hash = $passwordHash;
    }

    /**
     * Get password_hash
     *
     * @return string 
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * Set images
     *
     * @param string $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * Get images
     *
     * @return string 
     */
    public function getImages()
    {
        return $this->images;
    }
}
