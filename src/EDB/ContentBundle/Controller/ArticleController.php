<?php

namespace EDB\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;


class ArticleController extends Controller
{
    private function returnRequest($template, $data)
    {
        $view = View::create($data);
        if (strpbrk(":", $template))
            $view->setTemplate(new TemplateReference($template));
        else
            $view->setTemplate(new TemplateReference('EDBContentBundle', 'Resource', $template));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    private function returnOk($template, $data)
    {
        $data['success'] = true;

        return $this->returnRequest($template, $data);
    }


    private function returnError($message)
    {
        $data = array();

        $data['success'] = false;
        $data['message'] = $message;

        return $this->returnRequest('error', $data);
    }


    private function identifierAsArticle($identifier)
    {
        if (is_object($identifier))
            return $identifier;

        $o = $this->get('edbcontentbundle.articles')->getById($identifier);

        return $o;
    }


    public function listAction(Request $request)
    {
        $r = $this->get('edbcontentbundle.articles')->getAll();
        foreach ($r as $o) {
            $description = $this->get('edbcontentbundle.articles')->getResource($o, 'description');
            $this->get('edbcontentbundle.articles')->put($o, $o->getTitle(), $description);
        }

        return $this->returnError('any list action is prohibited for now');
    }


    public function searchAction(Request $request, $category_id)
    {
        $limit = 50;
        $offset = 0;

        $data = array();

        $query = trim($request->query->get('query'));
        if (strlen($query) < 1)
            return $this->returnError('query is empty');

        $data['category_id'] = $category_id;
        $data['articles'] = array();

        $categories = $this->get('edbcontentbundle.categories')->getAllChildren($category_id);
        $categories[] = $category_id;

        $articles = $this->get('edbcontentbundle.articles')->searchUnderCategories($categories, $query, $limit, $offset);
        foreach ($articles as $article) {
            $a = array();
            $a['id'] = $article->getId();
            $a['title'] = $article->getTitle();
            $a['category_id'] = $article->getCategory();
            $a['description'] = $article->getDescription();
            $a['description_short'] = $a['description'];
            $temp = explode("<br", $a['description']);
            $temp = explode("</h", $temp[0]);
            $a['description_short'] = trim(strip_tags($temp[0]));
            if (strlen($a['description_short']) > 40) {
                $a['description_short'] = substr($a['description_short'], 0, 50).'...';
            }
            $data['articles'][] = $a;
        }

        $data['count'] = count($data['articles']);
        $data['limit'] = $limit;
        $data['offset'] = $offset;

        return $this->returnOk('search', $data);
    }


    public function doAction(Request $request, $identifier)
    {
        $method = $request->getMethod();

        if ($this->get('security.context')->isGranted('ROLE_USER') === false && $method != 'GET') {
            return $this->returnError('access denied');
        }

        $o = $this->identifierAsArticle($identifier);
        if (!$o && $method != 'PUT')
            return $this->returnError('article not found, identifier '.$identifier.' is invalid');

        if ($method == 'GET') {
            return $this->getAction($request, $o);
        } else if ($method == 'PUT') {
            return $this->putAction($request, $o);
        } else if ($method == 'DELETE') {
            return $this->deleteAction($request, $o);
        }

        return $this->returnError('invalid method');
    }


    private function getAction(Request $request, $identifier)
    {
        $o = $this->identifierAsArticle($identifier);
        if (!$o)
            return $this->returnError('article not found');

        $articles_s = $this->get('edbcontentbundle.articles');

        $data = array();
        $data['id'] = $o->getId();
        $data['title'] = $o->getTitle();
        $data['category_id'] = $o->getCategory();
        $data['description'] = $o->getDescription();
        $data['portrait'] = $articles_s->getData($o, 'portrait');

        return $this->returnOk('get', $data);
    }


    private function putAction(Request $request, $identifier)
    {
        $o = $this->identifierAsArticle($identifier);

        $title = strip_tags(trim($request->request->get('title')));
        if (strlen($title) < 1)
            return $this->returnError('article title not defined');
        $description = $request->request->get('description');
        $category_id = intval($request->request->get('category_id'));
        $portrait = trim($request->request->get('portrait'));
        $stockable = trim($request->request->get('stockable')) == 'true' ? true : false;

        $articles_s = $this->get('edbcontentbundle.articles');

        $o = $articles_s->put($o, $title, $description, $stockable);
        if (!$o)
            return $this->returnError('unable to create article');
        $articles_s->setCategory($o, $category_id);
        $articles_s->setData($o, 'portrait', $portrait);
        
        $data = array();
        $data['id'] = $o->getId();
        $data['title'] = $o->getTitle();
        $data['category_id'] = $category_id;
        $data['description'] = $description;
        $data['portrait'] = $portrait;

        return $this->returnOk('get', $data);
    }


    private function deleteAction(Request $request, $identifier)
    {
        $data = array();

        $r = $this->get('edbcontentbundle.articles')->delete($identifier);
        if (!$r)
            return $this->returnError('article not found');

        return $this->returnOk('get', $data);
    }
}
