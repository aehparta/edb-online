<?php

namespace EDB\FrontpageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $data = array();
        $data['main_content_id'] = 1;
        return $this->render('EDBFrontpageBundle:Default:index.html.twig', $data);
    }

    public function browseAction($category_id = 0)
    {
        $data = array();
        $data['category_id'] = $category_id;
        if ($category_id < 1) {
            $category = $this->get('edbcontentbundle.categories')->getByTitle('Articles');
            $data['category_id'] = $category->getId();
        }

        return $this->render('EDBFrontpageBundle:Default:browse.html.twig', $data);
    }

    public function searchAction()
    {
        $data = array();

        return $this->render('EDBFrontpageBundle:Default:search.html.twig', $data);
    }
}
