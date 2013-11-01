<?php

namespace Aehparta\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;

class FormController extends Controller
{
    public function confirmAction(Request $request)
    {
        $data = array();
        $data['title'] = $request->query->get('title', 'Confirmation required');
        $data['title_save'] = $request->query->get('title_save', 'Yes');
        $data['title_delete'] = $request->query->get('title_delete', '');
        $data['title_cancel'] = $request->query->get('title_cancel', 'Cancel');
        $data['message'] = $request->query->get('message', 'Do you confirm requested action?');
        return $this->render('AehpartaFormBundle:Form:confirm.html.twig', $data);
    }

    public function confirmDeleteAction(Request $request)
    {
        $data = array();
        $data['title'] = $request->query->get('title', 'Confirm delete');
        $data['title_save'] = $request->query->get('title_save', '');
        $data['title_delete'] = $request->query->get('title_delete', 'Delete');
        $data['title_cancel'] = $request->query->get('title_cancel', 'Cancel');
        $data['message'] = $request->query->get('message', 'Do you confirm requested action?');
        return $this->render('AehpartaFormBundle:Form:confirm.html.twig', $data);
    }
}
