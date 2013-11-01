<?php

namespace EDB\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attachment
 */
class Attachment
{
    /**
     * @var integer
     */
    private $id;


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
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $file;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;


    /**
     * Set title
     *
     * @param string $title
     * @return Attachment
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Attachment
     */
    public function setFile($file)
    {
        $this->file = $file;
    
        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Attachment
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Attachment
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Attachment
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @ORM\prePersist
     */
    public function onCreate()
    {
        $this->created = new \DateTime();
        $this->modified = $this->created;
    }

    /**
     * @ORM\preUpdate
     */
    public function onUpdate()
    {
        $this->modified = new \DateTime();
    }
    /**
     * @var string
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return Attachment
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @var string
     */
    private $mime;


    /**
     * Set mime
     *
     * @param string $mime
     * @return Attachment
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
    
        return $this;
    }

    /**
     * Get mime
     *
     * @return string 
     */
    public function getMime()
    {
        return $this->mime;
    }
    /**
     * @var boolean
     */
    private $hidden;


    /**
     * Set hidden
     *
     * @param boolean $hidden
     * @return Attachment
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    
        return $this;
    }

    /**
     * Get hidden
     *
     * @return boolean 
     */
    public function getHidden()
    {
        return $this->hidden;
    }
    /**
     * @var integer
     */
    private $screenshot;


    /**
     * Set screenshot
     *
     * @param integer $screenshot
     * @return Attachment
     */
    public function setScreenshot($screenshot)
    {
        $this->screenshot = $screenshot;
    
        return $this;
    }

    /**
     * Get screenshot
     *
     * @return integer 
     */
    public function getScreenshot()
    {
        return $this->screenshot;
    }
}