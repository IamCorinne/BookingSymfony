<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
//enlevé car géré avec @ORM\HasLifecycleCallbacks dans Entity/Ad.php
//use Cocur\Slugify\Slugify;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    //suite à la modif dans config/packages/security.yaml pour bcrypt du mdt, on crée la variable privée tjrs pour sécuriser
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load (ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        //on déclare la variable pour utiliser fzaninotto/faker 
        //Factory est une class
        $faker=Factory::create('FR-fr');
        


////////////////////USERS->utilisateurs///////////////////////
//gestion des roles selon si adm utilisateurs
//role admin global
$adminRole=new Role();
$adminRole->setTitle("ROLE_ADMIN");
$manager->persist($adminRole);
//role utilisateur admin partiel
$adminUser=new User();
$adminUser  ->setFirstName('Iam')
            ->setLastName('Corinne')
            ->setEmail('sauviacc@hotmail.fr')
            ->setHash($this->encoder->encodePassword($adminUser,'password'))
            ->setAvatar('https://vendeephoto.fr/wp-content/uploads/2019/05/pexels-photo-1770310.jpeg')
            ->setIntroduction($faker->sentence())
            ->setDescription("<p>".join("</p><p>",$faker->paragraphs(5))."</p>")
            ->AddUserRole($adminRole)
            ;
$manager->persist($adminUser);

//on crée la variable user qui est un tableau contenant les users qui s'inscrivent(donc tableau vide)
//on fait une boucle pour récupérer toutes les valeurs (ici faite avec faker=fausse données)
$users=[];
//pour générer un avatar avec faker selon si profil homme ou femme
$genres=['male','female'];

for($i=1;$i<=10;$i++)
{
    //on instancie 
    $user= new User();
    //pour générer un avatar avec faker selon si profil homme ou femme
    $genre=$faker->randomElement($genres);
    $avatar='https://randomuser.me/api/portraits/';
    $avatarId=$faker->numberBetween(1,99).'.jpg';
    $avatar .=($genre=='male'? 'men/':'women/'). $avatarId;

    //pour crypter le mdp
    $hash=$this->encoder->encodePassword($user,'password');
    
    //on hydrate
    $description="<p>".join("</p><p>",$faker->paragraphs(5))."</p>";
    $user->setDescription($description)
        ->setFirstname($faker->firstname)
        ->setLastname($faker->lastname)
        ->setEmail($faker->email)
        ->setIntroduction($faker->sentence())
        ->setHash($hash)
        ->setAvatar($avatar)
        ;
        //on enregistre
        $manager->persist($user);
        $users[]=$user;
}




////////////////////ADVERT->annonces/////////////////////////        
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
        //$slug=$slugify->slugify($title);
        $coverImage=$faker->imageUrl(1000,350);
        $introduction=$faker->paragraph(2);
        $content="<p>".join("</p><p>",$faker->paragraphs(5))."</p>";
        //on crée l'user, l'auteur (ici l'auteur est aléatoire car on utilise faker pour l'exemple)
        $user=$users[mt_rand(0,count($users)-1)];

        //avant faker
        //$ad->setTitle("titre de l'annonce n° $i")
        //avec faker on utilise $title défini plus haut $title=$faker->sentence();
        $ad->setTitle($title)
        //enlevé car géré avec @ORM\HasLifecycleCallbacks dans Entity/Ad.php
            //->setSlug($slug)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(30,300))
            ->setRooms(mt_rand(2,5))
            ->setAuthor($user)
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

////////////////////BOOKING->annonces/////////////////////////          
        //Gestion des réservations
        for($k=1;$k<=\mt_rand(0,5);$k++)
        {
            //on crée une nouvelle réservation
            $booking=new Booking();
            $createdAt = $faker->dateTimeBetween('-6 months');
            $startDate= $faker->dateTimeBetween('-3 months');
            $duration = mt_rand(3,10);
            $endDate = (clone $startDate)->modify("+ $duration days");
            $amount = $ad->getPrice()* $duration;

            //lier le booker = le user qui reserve
            $booker = $users[mt_rand(0,count($users)-1)];
            $comment = $faker->paragraph();

            //configurer la réservation de base
            $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($comment)
                    ;

            //persister les datas
            $manager->persist($booking);

            //gestions des commentaires pour l'exemple une fois sur deux
            if(mt_rand(0,1))
            {
            //on instancie après la réservation le commentaire de l user sur sa résa
            $comment = new Comment();
            $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1,5))
                    ->setAuthor($booker)
                    ->setAd($ad)
                    ;
            //on persist ce commentaire
            $manager->persist($comment);
            }
        }

        }
        $manager->flush();
    }
}
