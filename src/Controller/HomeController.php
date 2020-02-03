<?php
// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Pour créer une page : - faire un namespace : chemin du controller
//                       - une fonction public (classe)
//                       - une route
//                       - une réponse

class HomeController extends AbstractController {
    /**
     * Création de notre première route
     * @Route("/",name="homePage")
     * 
     */
    public function home(){
        return $this->render('home.html.twig',['title'=>'Titre de la page Youpi']);
    }
    
}
