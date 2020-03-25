<?php
// src/Controller/HomeController.php
namespace App\Controller;


use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function home(AdRepository $adRepo, UserRepository $userRepo){
        return $this->render('home.html.twig',
        ['title'=>"Site d'annonces",
        'ads'=>$adRepo->findBestAds(6),
        'users'=>$userRepo->findBestUsers(4)
        ]);
    }
    
}
