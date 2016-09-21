<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class SecurityController extends MainController
{
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $this->setData(array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
        ));

        return $this->render(
            'security/login.html.twig',
            $this->getData()
        );
    }
}