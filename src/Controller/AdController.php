<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Permet d'afficher une liste d'annonces
     * @Route("/ads", name="ads_list")
     */
     //avant public function index() et nouveau
    public function index(AdRepository $repo)
    {
        //on crée la variable repo et on demande à doctrine d'aller chercher avec la méthode getRepository un répertoire de données de la class
        $repo=$this->getDoctrine()->getRepository(Ad::class);
        //on demande gràce à la variable $repo, on va chercher toutes les annonces avec findAll
        //et on oublie pas d'importer la class Ad (clique droit) et de retourner les données 'ads'=>$ads ligne 27 ou presque
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'ads'=>$ads
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_single")
     * 
     * @return Response
     */


    //on recherche un élément par son unique slug son argument et aussi par injection de dépendance du répertoire(ça change de -id)
    //ça c'était avant, le ParamConverter public function show($slug,AdRepository $repo) on enlève l'injection de dépendance et on rajoute la class Ad
    public function show($slug,Ad $ad)
    {
        //ça c'était avant le ParamConverter 
        //$ad = $repo->findOneBySlug($slug);

        //on renvoie en 1 paramètre la page et en 2 un tableau
        return $this->render('ad/show.html.twig',['ad'=>$ad]);
    }
}
