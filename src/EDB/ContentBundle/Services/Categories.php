<?php

namespace EDB\ContentBundle\Services;

use EDB\ContentBundle\Entity\Category;


class Categories
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


    public function getByTitle($title)
    {
        $r = $this->db->getRepository($this->className)->findBy(array('title' => $title, 'deleted' => false));
        if (!$r)
            return false;

        foreach ($r as $o) {
            if (count($o->getParents()) < 1)
                return $o;
        }

        return false;
    }


    public function getByParent($parent_id)
    {
        $r = $this->db->getRepository($this->className)->findBy(array('parent' => $parent_id, 'deleted' => false), array('title' => 'ASC'));
        if (!$r)
            return array();

        return $r;
    }


    public function getAllChildren($parent_id, &$children = array())
    {
        $r = $this->getByParent($parent_id);

        if (count($r) > 0) {
            $children = array_merge($children, $r);

            foreach ($r as $c) {
                $this->getAllChildren($c->getId(), $children);
            }
        }

        return $children;
    }


    public function getAll()
    {
        return $this->db->getRepository($this->className)->findBy(array('deleted' => false));
    }


    public function put($o, $title, $description, $parent = 0)
    {
        if (!$o)
            $o = new Category();

        $o->setTitle($title);
        $o->setDescription($description);
        $o->setParent($parent);
        $o->setDeleted(false);

        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }


    public function setParent($o, $parent_id)
    {
        $o->setParent($parent_id);
        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }


    public function getParentTree($o, $parents_array = array())
    {
        $parent = $o->getParent();

        if ($parent < 1)
            return array_reverse($parents_array);

        $o = $this->getById($parent);
        if (!$o)
            return array_reverse($parents_array);

        $parents_array[] = $o;

        return $this->getParentTree($o, $parents_array);
    }
}
