<?php

namespace EDB\BrowserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FormsController extends Controller
{
    public function categoryAction($category_id)
    {
        $category = $this->get('edbcontentbundle.categories')->getById($category_id);

        $data = array();
        $data['category'] = $category->getTitle();
        $data['category_id'] = $category->getId();

        return $this->render('EDBBrowserBundle:Forms:category.html.twig', $data);
    }

    public function articleAction($category_id, $article_id)
    {
        $category = $this->get('edbcontentbundle.categories')->getById($category_id);
        $article = $this->get('edbcontentbundle.articles')->getById($article_id);

        if ($article) {
            if (!$category) {
                $category = $this->get('edbcontentbundle.categories')->getById($article->getCategory());
            }
        }

        $data = array();
        $data['category'] = $category->getTitle();
        $data['category_id'] = $category->getId();

        $data['id'] = $article_id;
        $data['title'] = '';
        $data['description'] = '';
        $data['portrait'] = '';

        if ($article) {
            $data['title'] = $article->getTitle();
            $data['description'] = $article->getDescription();
            $data['portrait'] = $this->get('edbcontentbundle.articles')->getData($article, 'portrait');
        }

        $data['attachments'] = array();
        if ($article) {
            foreach ($article->getAttachments() as $aid) {
                $o = $this->get('edbcontentbundle.attachments')->getById($aid);
                if (!$o) {
                    $this->get('edbcontentbundle.articles')->deleteAttachment($article, $aid);
                    continue;
                }

                $a = array();
                $a['id'] = $o->getId();
                $a['title'] = $o->getName();
                $a['mime'] = $o->getMime();
                $mime_a = explode('/', $o->getMime(), 2);
                $a['mime_family'] = $mime_a[0];
                $a['mime_type'] = $mime_a[1];
                $a['url'] = $this->get('edbcontentbundle.attachments')->getUrl($o);
                $a['screenshot'] = false;
                $a['hidden'] = $o->getHidden();
                
/*                if ($o->getScreenshot() > 0) {
                    $a['screenshot'] = $this->get('edbcontentbundle.attachments')->getUrl($o->getScreenshot());
                } else if ($a['mime_type'] == 'pdf') {
                    $image = new \Imagick($this->get('edbcontentbundle.attachments')->getPath($o).'[0]');
                    $image->setResolution(600, 600);
                    $image->resampleImage(600, 600, \Imagick::FILTER_LANCZOS, 1);
                    $image->resizeImage(1200, 0, \Imagick::FILTER_LANCZOS, 0);
                    $image->setImageUnits(\Imagick::RESOLUTION_PIXELSPERINCH);
                    $image->setImageFormat( "png" );
                    $tmpfile = '/tmp/edb-pdf-screenshot-tmp.'.uniqid().'.png';
                    $image->writeImage($tmpfile);

                    $sa = $this->get('edbcontentbundle.attachments')->create('Screenshot: '.$o->getName(), $o->getName().'.png', $tmpfile, true);
                    $this->get('edbcontentbundle.attachments')->setScreenshot($o, $sa->getId());
                    $this->get('edbcontentbundle.attachments')->setHidden($sa, true);
                    $a['screenshot'] = $this->get('edbcontentbundle.attachments')->getUrl($sa->getId());
                    $this->get('edbcontentbundle.articles')->addAttachment($article, $sa->getId());
                }*/
                
                $data['attachments'][] = $a;
            }
        }

        $data['imagelibrary'] = $this->get('edbcontentbundle.attachments')->getImageLibrary();

        return $this->render('EDBBrowserBundle:Forms:article.html.twig', $data);
    }
}
