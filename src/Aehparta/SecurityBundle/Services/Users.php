<?php

namespace Aehparta\SecurityBundle\Services;

use Aehparta\SecurityBundle\Entity\User;


class Users
{
    private $db = null;
    private $className = '';
    private $encoder = null;

    public function __construct($db, $className, $encoder)
    {
        $this->db = $db;
        $this->className = $className;
        $this->encoder = $encoder;
    }

    public function getByUsername($username)
    {
        $o = $this->db->getRepository($this->className)->findOneBy(array('username' => $username, 'deleted' => false));
        if (!$o)
            return false;

        return $o;
    }

    public function getByEmail($email)
    {
        $o = $this->db->getRepository($this->className)->findOneBy(array('email' => $email, 'deleted' => false));
        if (!$o)
            return false;

        return $o;
    }

    public function create($name, $username, $password, $email, $description = '')
    {
        $o = $this->getByUsername($username);
        if ($o)
            return false;

        $o = new User();
        $o->setName($name);
        $o->setUsername($username);
        $o->setEmail($email);
        $o->setDescription($description);
        $o->setDeleted(false);

        $encoder = $this->encoder->getEncoder($o);
        $password = $encoder->encodePassword($password, $o->getSalt());
        $o->setPassword($password);

        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }


}
