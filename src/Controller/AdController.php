<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
//use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
 //ici les fonctions sans paramètre (paramConverter) 

    /**
     * Permet d'afficher une liste d'annonces
     * @Route("/ads", name="ads_list")
     */
     //avant public function index() et nouveau
    public function index(AdRepository $repo)
    {
        //on crée la variable repo et on demande à doctrine d'aller chercher avec la méthode getRepository un répertoire de données de la class
        //$repo=$this->getDoctrine()->getRepository(Ad::class);
        //on demande gràce à la variable $repo, on va chercher toutes les annonces avec findAll
        //et on oublie pas d'importer la class Ad (clique droit) et de retourner les données 'ads'=>$ads ligne 34 ou presque
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'ads'=>$ads
        ]);
    }

//ici les fonctions avec paramètre (paramConverter) 

    /**
     * Permet d'afficher et de créer le form pour créer une annonce
     * @Route("/ads/new", name="ads_create")
     * @return Response
     */
    //pour utiliser $manager et faire le CRUD on a besoin de ObjectManager(ben non ca sera EntityManager) pour créer une instance
    //public function create(Request $request)
    public function create(Request $request,EntityManagerInterface $manager) 
    {
        //on crée l'objet
        $ad = new Ad();
                /*  ça c'était avant de créer une class spécifique de Form dans src/Form/AnnonceType
                    //on ajoute le FormBuilder c'a-d le constructeur symfony de formulaire
                    $formCreate = $this->createFormBuilder($ad)
                    //on ajoute les champs de l'entité de la page src/Entity/Ad.php que l'on souhaite dans le form
                                        ->add('title')
                                        ->add('introduction')
                                        ->add('content')
                                        ->add('rooms')
                                        ->add('price')
                                        ->add('coverImage')
                    //on crée le formulaire
                                        ->getForm()
                                        ; 
                  maintenant on fait voir ligne suivante                      
                */

        //pour le formulaire des sous dossiers des images
             /* en exemple : 
             $images = new Image();
             $images->setUrl('https://placehold.co/600x400')
                    ->setCaption('Titre image 1');
             $ad->addImage($images); */
             
        //création du formulaire
        $form = $this->createForm(AnnonceType::class,$ad); 
        
        //récupérer les données du formulaire
        $form->handleRequest($request);
        //pour vérif ce qui est récupérable:
        //dump($form);

        //pour la sécurité on met une condition
       if($form->isSubmitted() && $form->isValid())
        {
            //on gère les images rajoutées en supp 
           foreach($ad->getImages()as $image)
           {
               //on relie l'image à l'annonce
               $image->setAd($ad);
               //on sauvegarde l'image dans la bdd
               $manager->persist($image);
           }

           //on demande à doctrine d'enregistrer dans la BDD dans l'objet $manager
           $manager->persist($ad);
           $manager->flush();

           //afficher un message flash avant le renvoie à la page. le 1er paramètre est le nom du flash, ici success; le deuxième le message dynamique
           $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> créée avec succès");

           //redirection vers la page
           return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }  


        //on redirige vers la page de visualisation avec en paramètre la demande de création de la vue du form
        return $this->render('ad/new.html.twig',['form'=> $form->createView()]);
    }



    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_single")
     * 
     * @return Response
     */
    //on recherche un élément par son unique slug son argument et aussi par injection de dépendance du répertoire(ça change de -id)
    //ça c'était avant, le ParamConverter public function show($slug,AdRepository $repo) on enlève l'injection de dépendance et on rajoute la class Ad
    public function show($slug, Ad $ad)
    {
        //ça c'était avant le ParamConverter 
        //$ad = $repo->findOneBySlug($slug);

        //on renvoie en 1 paramètre la page et en 2 un tableau
        return $this->render('ad/show.html.twig',['ad'=>$ad]);
    }

    
    /**
     * Permet de modifier une annonce
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @return Response
     */
    public function edit(Ad $ad,Request $request,EntityManagerInterface $manager )
    {
        $form=$this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($ad->getImages() as $image)
            {
                $image -> setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash("success","Les modifications sont faites.");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }
        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);
    }
}
