<?php

namespace EDB\BrowserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BrowserController extends Controller
{
    public function indexAction($category_id)
    {
        $category = $this->get('edbcontentbundle.categories')->getById($category_id);

        $data = array();
        $data['category'] = $category->getTitle();
        $data['category_id'] = $category->getId();

        return $this->render('EDBBrowserBundle:Browser:index.html.twig', $data);
    }


    private function parseCategoryParents($category, &$data)
    {
        $parents = $this->get('edbcontentbundle.categories')->getParentTree($category);
        foreach ($parents as $pp) {
//             if ($pp->getParent() < 1)
//                 break;
            $p = array();
            $p['id'] = $pp->getId();
            $p['title'] = $pp->getTitle();
            $data['parents'][] = $p;
        }
    }

    public function browseAction($category_id)
    {
        $show_only_undocumented = false;

        $category = $this->get('edbcontentbundle.categories')->getById($category_id);

        $data = array();
        $data['category'] = $category->getTitle();
        $data['category_id'] = $category->getId();
        $data['categories'] = $this->get('edbcontentbundle.categories')->getByParent($category_id);
        $data['parent'] = 0;
        $data['parents'] = array();
        $data['category_path'] = '';
        $data['articles'] = array();

        $data['parent'] = $category->getParent();
        $data['parents'] = array();

        $this->parseCategoryParents($category, $data);

        $articles = $this->get('edbcontentbundle.articles')->getByCategory($category_id);
        foreach ($articles as $article) {
            if ($show_only_undocumented) {
                if (strlen(strip_tags($article->getDescription())) > 0)
                    continue;
            }

            $a = array();
            $a['id'] = $article->getId();
            $a['category_id'] = $article->getCategory();
            $titles = explode(',', $article->getTitle());
            foreach ($titles as $title) {
                $a['title'] = trim($title);
                $data['articles'][$a['title']] = $a;
            }
        }
        ksort($data['articles']);

        return $this->render('EDBBrowserBundle:Browser:browse.html.twig', $data);
    }

    public function searchAction()
    {
        $data = array();

        return $this->render('EDBBrowserBundle:Forms:search.html.twig', $data);
    }

    public function categoryFormAction($category_id)
    {
        $category = $this->get('edbcontentbundle.categories')->getById($category_id);

        $data = array();
        $data['category'] = $category->getTitle();
        $data['category_id'] = $category->getId();

        $this->parseCategoryParents($category, $data);

        return $this->render('EDBBrowserBundle:Forms:category.html.twig', $data);
    }
}
