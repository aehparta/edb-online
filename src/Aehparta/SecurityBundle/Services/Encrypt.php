<?php

namespace Aehparta\SecurityBundle\Services;


class Encrypt
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    function randomAscii($len = 16, $chars = false)
    {
        if (!$chars) {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $chars_len = strlen($chars);

        $id = '';
        for ($i = 0; $i < $len; $i++)
        {
            $pos = mt_rand(0, $chars_len - 1);
            $id .= $chars[$pos];
        }

        return $id;
    }

}