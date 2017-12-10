<?php

namespace EDB\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockItem
 */
class StockItem
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
     * @var integer
     */
    private $item_id;

    /**
     * @var integer
     */
    private $user_id;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $package;

    /**
     * @var integer
     */
    private $pin_count;

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
     * Set item_id
     *
     * @param integer $itemId
     * @return StockItem
     */
    public function setItemId($itemId)
    {
        $this->item_id = $itemId;
    
        return $this;
    }

    /**
     * Get item_id
     *
     * @return integer 
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return StockItem
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    
        return $this;
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

    /**
     * Set quantity
     *
     * @param string $quantity
     * @return StockItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    
        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set package
     *
     * @param string $package
     * @return StockItem
     */
    public function setPackage($package)
    {
        $this->package = $package;
    
        return $this;
    }

    /**
     * Get package
     *
     * @return string 
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set pin_count
     *
     * @param integer $pinCount
     * @return StockItem
     */
    public function setPinCount($pinCount)
    {
        $this->pin_count = $pinCount;
    
        return $this;
    }

    /**
     * Get pin_count
     *
     * @return integer 
     */
    public function getPinCount()
    {
        return $this->pin_count;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return StockItem
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
     * @return StockItem
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
     * @return StockItem
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
    private $storage;

    /**
     * @var string
     */
    private $description;


    /**
     * Set storage
     *
     * @param string $storage
     * @return StockItem
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    
        return $this;
    }

    /**
     * Get storage
     *
     * @return string 
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return StockItem
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
     * @var string
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return StockItem
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
    private $note;


    /**
     * Set note
     *
     * @param string $note
     * @return StockItem
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }
}