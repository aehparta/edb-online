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
        $data['stockable'] = true;
        
        if ($article) {
            $data['title'] = $article->getTitle();
            $data['description'] = $article->getDescription();
            $data['portrait'] = $this->get('edbcontentbundle.articles')->getData($article, 'portrait');
            $data['stockable'] = $article->getStockable();
        }

        return $this->render('EDBBrowserBundle:Forms:article.html.twig', $data);
    }

    public function articleAttachmentsAction($article_id)
    {
        $article = $this->get('edbcontentbundle.articles')->getById($article_id);

        $data = array();
        $data['article_id'] = $article_id;
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

                $data['attachments'][] = $a;
            }
        }

        $data['imagelibrary'] = $this->get('edbcontentbundle.attachments')->getImageLibrary();

        return $this->render('EDBBrowserBundle:Forms:article_attachments.html.twig', $data);
    }
    
    public function articleStockAction($article_id, $user_id = 0)
    {
        if (intval($user_id) < 1) {
            $user_id = $this->getUser()->getId();
        }
        $stock = $this->get('edbcontentbundle.stock');
        
        $data = array();
        $data['article_id'] = $article_id;
        $items = array();
        if (intval($article_id) > 0) {
            $article = $this->get('edbcontentbundle.articles')->getById($article_id);
            $names = explode(',', $article->getTitle());
            foreach ($names as $name) {
                $data['names'][] = trim($name);
            }
            $items = $stock->getAllByArticleAndUser($article_id, $user_id);
            $data['display_links'] = false;
        } else {
            $data['names'] = array();
            $items = $stock->getAllByUser($user_id);
            $data['display_links'] = true;
        }
        
        $data['stock'] = array();
        foreach ($items as $item) {
            if ($item->getQuantity() != '0') {
                continue;
            }
            $data['stock'][] = array(
                'id' => $item->getId(),
                'article_id' => $item->getItemId(),
                'name' => $item->getName(),
                'package' => $item->getPackage(),
                'quantity' => $item->getQuantity(),
                'note' => $item->getNote(),
                'storage' => $item->getStorage(),
            );
        }
        foreach ($items as $item) {
            if ($item->getQuantity() == '0') {
                continue;
            }
            $data['stock'][] = array(
                'id' => $item->getId(),
                'article_id' => $item->getItemId(),
                'name' => $item->getName(),
                'package' => $item->getPackage(),
                'quantity' => $item->getQuantity(),
                'note' => $item->getNote(),
                'storage' => $item->getStorage(),
            );
        }
        
        return $this->render('EDBBrowserBundle:Forms:stock.html.twig', $data);
    }
}
