<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends MainController
{
    public function loginAction()
    {
        return $this->render( "admin/login.html.twig", $this->getData());
    }
}
