<?php

namespace EDB\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;


class ResourceController extends Controller
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


    private function identifierAsResource($identifier)
    {
        if (is_object($identifier))
            return $identifier;

        $resource = $this->get('edbcontentbundle.resources')->getById($identifier);

        return $resource;
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

        $resource = $this->identifierAsResource($identifier);
        if (!$resource && $method != 'PUT')
            return $this->returnError('resource not found, identifier '.$identifier.' is invalid');

        if ($method == 'GET') {
            return $this->getAction($request, $resource);
        } else if ($method == 'PUT') {
            return $this->putAction($request, $resource);
        } else if ($method == 'DELETE') {
            return $this->deleteAction($request, $resource);
        }

        return $this->returnError('invalid method');
    }


    public function getAction(Request $request, $identifier)
    {
        $resource = $this->identifierAsResource($identifier);
        if (!$resource)
            return $this->returnError('resource not found');

        $data = array();
        $data['id'] = $resource->getId();
        $data['title'] = $resource->getTitle();
        $data['content'] = $resource->getContent();

        return $this->returnOk('get', $data);
    }


    public function putAction(Request $request, $identifier)
    {
        $resource = $this->identifierAsResource($identifier);

        $title = $request->request->get('title');
        $content = $request->request->get('content');

        $resource = $this->get('edbcontentbundle.resources')->put($resource, $content, $title);
        if (!$resource)
            return $this->returnError('unable to create resource');

        $data = array();
        $data['id'] = $resource->getId();
        $data['title'] = $resource->getTitle();
        $data['content'] = $resource->getContent();

        return $this->returnOk('get', $data);
    }


    public function deleteAction(Request $request, $identifier)
    {
        $data = array();

        return $this->returnError('not implemented');
    }
}
