<?php

namespace EDB\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;


class AttachmentController extends Controller
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


    public function doAction(Request $request, $attachment_id)
    {
        $method = $request->getMethod();

        if ($this->get('security.context')->isGranted('ROLE_USER') === false && $method != 'GET') {
            return $this->returnError('access denied');
        }

        if ($method == 'POST') {
            return $this->postAction($request);
        } else if ($method == 'DELETE') {
            return $this->deleteAction($request, $attachment_id);
        }

        return $this->returnError('invalid method');
    }


    public function postAction(Request $request)
    {
        $data = array();
        
        if (empty($_FILES))
            return $this->returnError('invalid request');
        
        error_log("upload");

        $filename = $_FILES['Filedata']['name'];
        $tmpfile = $_FILES['Filedata']['tmp_name'];
        $o = $this->get('edbcontentbundle.attachments')->create($filename, $filename, $tmpfile);

        $data['id'] = $o->getId();
        $data['url'] = $this->get('edbcontentbundle.attachments')->getUrl($o->getId());
        $data['title'] = $o->getTitle();

        $article_id = $request->request->get('article_id');
        if ($article_id > 0) {
            $this->get('edbcontentbundle.articles')->addAttachment($article_id, $o->getId());
        }

        return $this->returnOk('get', $data);
    }


    public function deleteAction(Request $request, $attachment_id)
    {
        $data = array();
        $this->get('edbcontentbundle.attachments')->delete($attachment_id);
        return $this->returnOk('get', $data);
    }
}
