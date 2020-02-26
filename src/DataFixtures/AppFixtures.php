<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
//enlevé car géré avec @ORM\HasLifecycleCallbacks dans Entity/Ad.php
//use Cocur\Slugify\Slugify;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load (ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        //on déclare la variable pour utiliser fzaninotto/faker 
        //Factory est une class
        $faker=Factory::create('FR-fr');
        //enlevé car géré avec @ORM\HasLifecycleCallbacks dans Entity/Ad.php
        ////on déclare la variable pour utiliser slugify et sa class Slugify
        //$slugify=new Slugify();

        //pour récupérer tous les champs [titres, price etc ] on instancie l'objet
        //Idéale on fait une boucle des éléments qui rempliront notre BDD
        for($i=1; $i<=30; $i++)
        {
        $ad = new Ad();

        $title=$faker->sentence();
        //enlevé car géré avec @ORM\HasLifecycleCallbacks dans Entity/Ad.php
        $slug=$slugify->slugify($title);
        $coverImage=$faker->imageUrl(1000,350);
        $introduction=$faker->paragraph(2);
        $content="<p>".join("</p><p>",$faker->paragraphs(5))."</p>";

        //avant faker
        //$ad->setTitle("titre de l'annonce n° $i")
        //avec faker on utilise $title défini plus haut $title=$faker->sentence();
        $ad->setTitle($title)
        //enlevé car géré avec @ORM\HasLifecycleCallbacks dans Entity/Ad.php
            ->setSlug($slug)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(30,300))
            ->setRooms(mt_rand(2,5))
        ;
        
        $manager->persist($ad);

        //cette boucle est bien l'autre boucle
        for($j=1;$j<=\mt_rand(2,5);$j++)
        {
            //on crée une nouvelle instance de l'image
            $image = new Image();
            $image->setUrl($faker->imageUrl())
            ->setCaption($faker->sentence())
            ->setAd($ad)
            ;

            //on sauvegarde
            $manager->persist($image);
        }

        }
        $manager->flush();
    }
}
