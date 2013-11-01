<?php

namespace Aehparta\SecurityBundle\Controller;

use Aehparta\ResponseBundle\Controller\ResponseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends ResponseController
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        $error = false;
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $data = array();
        $data['last_username'] = $session->get(SecurityContext::LAST_USERNAME);
        $data['error'] = $error;

        return $this->render('AehpartaSecurityBundle:Default:login.html.twig', $data);
    }

    private function tryRegister(Request $request)
    {
        $request = $request->request;

        $name = trim($request->get('name'));
        $username = trim($request->get('username'));
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');
        $email = trim($request->get('email'));

        if (mb_strlen($name) < 1)
            return 'Name is empty.';
        if (mb_strlen($username) < 3)
            return 'Username is too short, must be atleast 3 characters.';
        if ($password1 != $password2)
            return 'Password fields not matching.';
        if (mb_strlen($password1) < 5)
            return 'Passwords less than 5 characters not allowed.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return 'Invalid email.';

        $apikey = $this->get('aehpartasecuritybundle.encrypt')->randomAscii(32);
        $e = $this->get('aehpartasecuritybundle.users')->create($name, $username, $password1, $email, $apikey);
        if (!$e)
            return 'Failed creating user, already exists.';

        return false;
    }

    public function registerAction(Request $request)
    {
        $error = false;
        $data = array();
        $data['registered'] = false;
        $data['name'] = '';
        $data['username'] = '';
        $data['email'] = '';

        $method = $request->getMethod();

        if ($method == 'POST') {
            $error = $this->tryRegister($request);
            if ($error == false)
                $data['registered'] = true;
        } else if ($method == 'GET') {
        } else {
            return $this->render('AehpartaSecurityBundle:Default:error.html.twig', $data);
        }

        $data['error'] = $error;

        return $this->render('AehpartaSecurityBundle:Default:register.html.twig', $data);
    }

    public function resetAction(Request $request)
    {
        $error = false;

        $data = array();
        $data['reset_done'] = false;

        $method = $request->getMethod();

        if ($method == 'POST') {
            $email = trim($request->request->get('email'));
            $user = false;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $error = 'Invalid email.';
            else {
                $data['reset_done'] = true;
                $user = $this->get('aehpartasecuritybundle.users')->getByEmail($email);
                if ($user) {
                    $headers = "From: webmaster@edb-online.info\r\n".
                        "X-Mailer: PHP/".phpversion();
                    mail($email,
                        '[eDB] Reset password',
                        'Reset your password here: ...',
                        $headers);
                }
            }
        } else if ($method == 'GET') {
        } else {
            return $this->render('AehpartaSecurityBundle:Default:error.html.twig', $data);
        }

        $data['error'] = $error;

        return $this->render('AehpartaSecurityBundle:Default:reset.html.twig', $data);
    }
}
