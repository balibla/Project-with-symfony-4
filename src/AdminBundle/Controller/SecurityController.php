<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController{

    /**
     * @Route("/login", name="login")
     */
    public function login(){

        // Si le visiteur est déjà identifié, on le redirige vers la page register
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('profilepage');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('AdminBundle:Default:login.html.twig',[
            'last_username' => $lastUsername,
            'error' => $error,
            'current_menu' => 'login'
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

}