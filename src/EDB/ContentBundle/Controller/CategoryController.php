<?php

namespace EDB\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;


class CategoryController extends Controller
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


    private function identifierAsCategory($identifier)
    {
        if (is_object($identifier))
            return $identifier;

        $o = $this->get('edbcontentbundle.categories')->getById($identifier);

        return $o;
    }


    public function listAction(Request $request)
    {
        return $this->returnError('any list action is prohibited for now');
    }


    public function doAction(Request $request, $identifier)
    {
        $method = $request->getMethod();

        if ($this->get('security.context')->isGranted('ROLE_USER') === false && $method != 'GET') {
            return $this->returnError('access denied');
        }

        $o = $this->identifierAsCategory($identifier);
        if (!$o && $method != 'PUT')
            return $this->returnError('category not found, identifier '.$identifier.' is invalid');

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
        $o = $this->identifierAsCategory($identifier);
        if (!$o)
            return $this->returnError('category not found');

        $data = array();
        $data['id'] = $o->getId();
        $data['title'] = $o->getTitle();
        $data['description'] = $o->getDescription();
        $data['parent'] = $o->getParent();

        return $this->returnOk('get', $data);
    }


    private function putAction(Request $request, $identifier)
    {
        $o = $this->identifierAsCategory($identifier);

        $title = trim($request->request->get('title'));
        $description = trim($request->request->get('description'));
        $parent = $request->request->get('parent');
        if (strlen($title) < 1)
            return $this->returnError('category title not defined');

        $o = $this->get('edbcontentbundle.categories')->put($o, $title, $description, $parent);
        if (!$o)
            return $this->returnError('unable to create category');

        $data = array();
        $data['id'] = $o->getId();
        $data['title'] = $o->getTitle();
        $data['description'] = $o->getDescription();
        $data['parent'] = $o->getParent();

        return $this->returnOk('get', $data);
    }


    private function deleteAction(Request $request, $identifier)
    {
        $data = array();

        return $this->returnError('not implemented');
    }
}
