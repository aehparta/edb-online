<?php

namespace EDB\ContentBundle\Services;

use EDB\ContentBundle\Entity\Article;
use EDB\ContentBundle\Entity\Resource;


class Articles
{
    private $db = null;
    private $s_resource = null;
    private $className = '';


    public function __construct($db, $s_resource, $className)
    {
        $this->db = $db;
        $this->s_resource = $s_resource;
        $this->className = $className;
    }


    public function getAll()
    {
        return $this->db->getRepository($this->className)->findBy(array('deleted' => false));
    }


    public function getById($identifier)
    {
        $o = $this->db->getRepository($this->className)->findOneBy(array('id' => $identifier, 'deleted' => false));
        if (!$o)
            return false;

        return $o;
    }


    private function sortNumeric($a, $b)
    {
        return (intval($a->getTitle()) < intval($b->getTitle())) ? -1 : 1;
    }


    public function getByCategory($identifier)
    {
        $r = $this->db->getRepository($this->className)->findBy(array('category' => $identifier, 'deleted' => false), array('title' => 'ASC'));
        if (!$r)
            return array();

        $only_digits = true;
        foreach ($r as $o) {
            $title = strpbrk($o->getTitle(), ',-');
            if ($title) {
                $title = substr($o->getTitle(), 0, -(strlen($title)));
            } else {
                $title = $o->getTitle();
            }
            if (!ctype_digit($title)) {
                $only_digits = false;
                break;
            }

        }

        if ($only_digits) {
            usort($r, array($this, 'sortNumeric'));
        }

        return $r;
    }


    public function searchUnderCategories($categories, $search, $limit = 0, $offset = 0)
    {
        if (!is_array($categories))
            $categories = array($categories);

        $results = array();

        foreach ($categories as $category) {
            $category_id = false;
            if (is_object($category))
                $category_id = $category->getId();
            else
                $category_id = $category;

            $repository = $this->db->getRepository('EDBContentBundle:Article');
            $q = $repository->createQueryBuilder('o');

            $q->where(
                $q->expr()->andX(
                    $q->expr()->eq('o.category', ':category'),
                    $q->expr()->orX(
                        $q->expr()->like('o.title', ':search'),
                        $q->expr()->like('o.description', ':search')
                    )
                )
            );
            $q->andWhere('o.deleted = 0');

            $q->setParameter('category', $category_id);
            $q->setParameter('search', '%'.$search.'%');
            $q->orderBy('o.title', 'ASC');
            $q->setFirstResult($offset);
            $q->setMaxResults($limit);
            $query = $q->getQuery();

            $r = $query->getResult();
            if (!$r)
                continue;
            if (count($r) < 1)
                continue;

            $results = array_merge($results, $r);
        }

        return $results;
    }


    public function put($o, $title, $description = '', $stockable = true)
    {
        if (!$o) {
            $o = new Article();
            $o->setCategory(0);
            $o->setCategories(array());
            $o->setResources(array());
            $o->setAttachments(array());
        }

        $o->setTitle($title);
        $o->setDescription($description);
        $o->setStockable($stockable);
        $o->setDeleted(false);

        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }


    public function setCategory($o, $categoryId)
    {
        $o->setCategory($categoryId);
        $this->db->flush();

        return $o;
    }


    public function setResource($o, $key, $content)
    {
        $resources = $o->getResources();
        $resource = false;

        if (array_key_exists($key, $resources)) {
            $resource = $this->s_resource->getById($resources[$key]);
            $this->s_resource->put($resource, $content, $key);
        } else {
            $resource = $this->s_resource->put(false, $content, $key);
            $resources[$key] = $resource->getId();
            $o->setResources($resources);
            $this->db->flush();
        }

        return $o;
    }


    public function getResource($o, $key)
    {
        $resources = $o->getResources();
        $resource = false;

        if (array_key_exists($key, $resources)) {
            $resource = $this->s_resource->getById($resources[$key]);
            return $resource->getContent();
        }

        return "";
    }


    public function delete($o)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }

        $o->setDeleted(true);
        $this->db->flush();

        return true;
    }


    public function addAttachment($o, $attachment)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }

        if (is_object($attachment))
            $attachment = $attachment->getId();

        $attachments = $o->getAttachments();
        $attachments[] = $attachment;
        $o->setAttachments($attachments);

        $this->db->flush();

        return true;
    }


    public function deleteAttachment($o, $attachment_id)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }

        $attachments = $o->getAttachments();
        $attachments_new = array();
        foreach ($attachments as $attachment) {
            if ($attachment != $attachment_id)
                $attachments_new[] = $attachment;
        }
        $o->setAttachments($attachments_new);

        $this->db->flush();

        return true;
    }


    public function setData($o, $key, $content)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }

        $data = $o->getData();
        $data[$key] = $content;
        $o->setData($data);

        $this->db->flush();

        return true;
    }


    public function getData($o, $key)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }

        $data = $o->getData();
        if (array_key_exists($key, $data))
            return $data[$key];

        return false;
    }
}
