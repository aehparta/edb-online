<?php

namespace EDB\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 */
class Article
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
     * @var array
     */
    private $categories;

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
     * @return Article
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
     * Set categories
     *
     * @param array $categories
     * @return Article
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    
        return $this;
    }

    /**
     * Get categories
     *
     * @return array 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * @var array
     */
    private $resources;

    /**
     * @var array
     */
    private $attachments;


    /**
     * Set resources
     *
     * @param array $resources
     * @return Article
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    
        return $this;
    }

    /**
     * Get resources
     *
     * @return array 
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set attachments
     *
     * @param array $attachments
     * @return Article
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    
        return $this;
    }

    /**
     * Get attachments
     *
     * @return array 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
    /**
     * @var integer
     */
    private $category;


    /**
     * Set category
     *
     * @param integer $category
     * @return Article
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var array
     */
    private $data;


    /**
     * Set data
     *
     * @param array $data
     * @return Article
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @var string
     */
    private $description;


    /**
     * Set description
     *
     * @param string $description
     * @return Article
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @var boolean
     */
    private $stockable;


    /**
     * Set stockable
     *
     * @param boolean $stockable
     * @return Article
     */
    public function setStockable($stockable)
    {
        $this->stockable = $stockable;
    
        return $this;
    }

    /**
     * Get stockable
     *
     * @return boolean 
     */
    public function getStockable()
    {
        return $this->stockable;
    }
}