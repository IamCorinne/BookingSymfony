<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login( AuthenticationUtils $utils)
    {
        //on récupère la dernière error
        $error = $utils->getLastAuthenticationError();
        //sur le dernier user
        $username = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }
/**
 * Déconnexion d ela partie admin
 * @Route("/admin/logout",name="admin_account_logout")
 * @return void
 */
    public function logout()
    {

    }
}
