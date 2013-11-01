<?php

namespace EDB\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Aehparta\ResponseBundle\Controller\ResponseController;


class AttachmentController extends ResponseController
{
    public function doAction(Request $request, $attachment_id)
    {
        $method = $request->getMethod();

        $apikey = $request->request->get('apikey');
        $user = $this->get('aehpartasecuritybundle.users')->getByApikey($apikey);

        if ($user) {
            // accept apikey
        } else if ($this->get('security.context')->isGranted('ROLE_USER') === false && $method != 'GET') {
            return $this->returnError('EDBContentBundle:Resource:error.html.twig', 'access denied');
        }

        if ($method == 'POST' or $method == 'PUT') {
            return $this->putAction($request);
        } else if ($method == 'DELETE') {
            return $this->deleteAction($request, $attachment_id);
        }

        return $this->returnError('EDBContentBundle:Resource:error.html.twig', 'invalid method');
    }


    private function putAction(Request $request)
    {
        $data = array();

        if (empty($_FILES))
            return $this->returnError('EDBContentBundle:Resource:error.html.twig', 'invalid request');

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

        return $this->returnOk('EDBContentBundle:Resource:get.html.twig', $data);
    }


    private function deleteAction(Request $request, $attachment_id)
    {
        $data = array();
        $this->get('edbcontentbundle.attachments')->delete($attachment_id);
        return $this->returnOk('EDBContentBundle:Resource:get.html.twig', $data);
    }
}
