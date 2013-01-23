<?php

namespace Aehparta\ResponseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;

class ResponseController extends Controller
{
    public function returnRequest($template, $data)
    {
        $view = View::create($data);
        $view->setTemplate($template);

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    public function returnOk($template, $data)
    {
        $data['success'] = true;

        return $this->returnRequest($template, $data);
    }


    public function returnError($message)
    {
        $data = array();

        $data['success'] = false;
        $data['message'] = $message;

        return $this->returnRequest('error', $data);
    }
}
