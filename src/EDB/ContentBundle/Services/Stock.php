<?php

namespace EDB\ContentBundle\Services;

use EDB\ContentBundle\Entity\StockItem;


class Stock
{
    private $db = null;
    private $className = '';


    public function __construct($db, $className)
    {
        $this->db = $db;
        $this->className = $className;
    }


    public function getAllByUser($user_id)
    {
        return $this->db->getRepository($this->className)->findBy(array('user_id' => $user_id, 'deleted' => false), array('name' => 'ASC'));
    }


    public function getAllByArticleAndUser($article_id, $user_id)
    {
        return $this->db->getRepository($this->className)->findBy(array('item_id' => $article_id, 'user_id' => $user_id, 'deleted' => false), array('name' => 'ASC'));
    }


    public function getStockItem($article_id, $user_id, $stock_id)
    {
        return $this->db->getRepository($this->className)->findOneBy(array('item_id' => $article_id, 'user_id' => $user_id, 'id' => $stock_id, 'deleted' => false));
    }
    
    
    public function put($o, $article_id, $user_id, $name, $qty, $package, $note, $storage)
    {
        if (!$o) {
            $o = new StockItem();
            $o->setItemId($article_id);
            $o->setUserId($user_id);
        }

        $o->setName($name);
        $o->setQuantity($qty);
        $o->setPackage($package);
        $o->setNote($note);
        $o->setStorage($storage);
        $o->setDeleted(false);

        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }


    public function delete($o)
    {
        if (!is_object($o)) {
            return false;
        }

        $o->setDeleted(true);
        $this->db->flush();

        return true;
    }

}
