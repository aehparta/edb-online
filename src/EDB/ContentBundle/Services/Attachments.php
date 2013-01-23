<?php

namespace EDB\ContentBundle\Services;

use EDB\ContentBundle\Entity\Attachment;


class Attachments
{
    private $db = null;
    private $className = '';
    private $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


    function createRandomAscii($len)
    {
            $chars_len = strlen($this->chars);

            $id = '';
            for ($i = 0; $i < $len; $i++)
            {
                    $pos = mt_rand(0, $chars_len - 1);
                    $id .= $this->chars[$pos];
            }

            return $id;
    }


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


    public function getUrl($o)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }
        
        return '/uploads/'.$o->getFile();
    }

    public function getPath($o)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }

        return $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$o->getFile();
    }
        
    public function create($title, $filename, $tmpfile, $copy_dont_move = false, $path = false)
    {
        if (!$path)
            $path = $_SERVER['DOCUMENT_ROOT'].'/uploads';
        $rname = $this->createRandomAscii(16);
        $c1 = $rname[0];
        $c2 = $rname[1];
        $filepath = $c1.'/'.$c2.'/'.$rname.'_'.$filename;

        $target = $path.'/'.$filepath;

        if ($copy_dont_move) {
            copy($tmpfile, $target);
        } else {
            move_uploaded_file($tmpfile, $target);
        }

        $ext = pathinfo($target, PATHINFO_EXTENSION);
        $mime = 'application/octet-stream';
        if ($ext == 'pdf') {
            $mime = 'application/pdf';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $target);
            finfo_close($finfo);
        }

        $o = new Attachment();
        $o->setTitle($title);
        $o->setName($filename);
        $o->setFile($filepath);
        $o->setMime($mime);
        $o->setScreenshot(0);
        $o->setHidden(false);
        $o->setDeleted(false);

        $this->db->persist($o);
        $this->db->flush();

        return $o;
    }

    public function setScreenshot($o, $attachment_id)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }
        
        $o->setScreenshot($attachment_id);
        $this->db->flush();
        
        return true;
    }
    
    public function setHidden($o, $value)
    {
        if (!is_object($o)) {
            $o = $this->getById($o);
            if (!$o)
                return false;
        }
        
        $o->setHidden($value);
        $this->db->flush();
        
        return true;
    }

    public function delete($identifier)
    {
        $o = $this->getById($identifier);
        if (!$o)
            return false;

        $o->setDeleted(true);
        $this->db->persist($o);
        $this->db->flush();

        return true;
    }


    public function getImageLibrary()
    {
        $path = $_SERVER['DOCUMENT_ROOT'].'/images/library/';
        $data = array();

        $dh = opendir($path);
        if (!$dh)
            return $data;

        while (($file = readdir($dh)) !== false) {
            if (is_dir($file))
                continue;

            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mime = 'application/octet-stream';
            if ($ext == 'pdf') {
                $mime = 'application/pdf';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $path.$file);
                finfo_close($finfo);
            }

            $mime_a = explode('/', $mime, 2);
            if ($mime_a[0] == 'image') {
                $d = array();
                $d['title'] = $file;
                $d['mime'] = $mime;
                $d['mime_family'] = $mime_a[0];
                $d['mime_type'] = $mime_a[1];
                $d['url'] = '/images/library/'.$file;
                $data[] = $d;
            }
        }

        closedir($dh);

        return $data;
    }
}
