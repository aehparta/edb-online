<?php

namespace EDB\ContentBundle\Services;

use EDB\ContentBundle\Entity\Resource;


class Resources
{
    private $db = null;
    private $className = '';


    public function __construct($db, $className)
    {
        $this->db = $db;
        $this->className = $className;
    }


    public function getById($identifier)
    {
        $o = $this->db->getRepository($this->className)->findOneBy(array('id' => $identifier, 'deleted' => false));
        if (!$o)
            return false;

        return $o;
    }


    public function put($o, $content, $title = false)
    {
        if (!$o)
            $o = new Resource();

        $o->setContent($content);
        $o->setDeleted(false);

        if ($title)
            $o->setTitle($title);

        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }
}
