<?php

namespace OSU\CollageAPIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OSU\CollageAPIBundle\Entity\Image
 */
class Image
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $file_name
     */
    private $file_name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var bytea $image_data
     */
    private $image_data;

    /**
     * @var datetime $date
     */
    private $date;

    /**
     * @var integer $user_id
     */
    private $user_id;


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
     * Set file_name
     *
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;
    }

    /**
     * Get file_name
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set image_data
     *
     * @param bytea $imageData
     */
    public function setImageData($imageData)
    {
        $this->image_data = $imageData;
    }

    /**
     * Get image_data
     *
     * @return bytea 
     */
    public function getImageData()
    {
        return $this->image_data;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}