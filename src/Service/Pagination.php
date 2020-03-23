<?php

namespace App\Service;

use Twig\Environment;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination
{
    private $entityClass;
    private $limit=10;
    private $currentPage=1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    //charger le manager
    public function __construct(EntityManagerInterface $manager,Environment $twig,RequestStack $request,$templatePath)
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        //pour la pagination
        $this->twig = $twig;
        $this->templatePath =$templatePath;
    
    }

    // pour la factorisation de la pagination
    public function display()
    {
        //appel du moteur twig qui precise le template à utiliser
        $this->twig ->display($this->templatePath,
        [
            //options pou l'affichage des données
            //variables : route/page/pages
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }
    
    //1_ utiliser la pagination à partir de n'importe quel controller
    //on précise donc l'entité concernéee
    public function setEntityClass($entityClass)
    {
        //EntityClass doit être = data envoyé
        $this->entityClass=$entityClass;
        return $this;
    }
    public function getEntityClass()
    {
        return $this->entityClass;
    }
    
    //2_ quelle est la limite de page
    public function getLimit()
    {
        return $this->limit;
    }
    public function setLimit($limit)
    {
        $this->limit=$limit;
        return $this;
    }
    
    
    //3_ quelle est la page actuelle
    public function getPage()
    {
        return $this->currentPage;
    }
    public function setPage($page)
    {
        $this->currentPage=$page;
        return $this;
    }

    //4_récupérer le nbr de page
    public function getData()
    {
        //si il n y a pas d'entité de renseignée
        if (empty($this->entityClass))
        {
            throw new \Exception("setEntityclass n' a pas été renseigné dans le controller correspondant.");
        }
        //calculer à partir du quel on affiche
        //$start = $page * $limit - $limit;
        $offset= $this->currentPage * $this->limit - $this->limit;
        //demande au repository de trouver les elements
        //on va chercher le bon repository
        $repo = $this->manager->getRepository($this->entityClass);
        //on construit notre requête
        //'ads'=>$repo->findBy([],[],$limit,$start)
        $data=$repo->findBy([],[],$this->limit,$offset);

        return $data;
    }

    public function getPages()
    {
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route=$route;
        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

}