<?php

namespace App\Controller;

//use Doctrine\Common\Persistence\ObjectManager;

use App\Service\Statistics;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager,Statistics $statsService)
    {
        //Avant maintenant tout est dans le service Statistics:

        //on crée une requete directement avec l objet manager
        //$users=$manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        //dump($users);
        //$ads=$manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
        //dump($ads);
        //$bookings=$manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
        //dump($bookings);
        //$comments=$manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
        //dump($comments);

        //et ça aussi c'était avant:
        /* $users = $statsService->getUsersCount();
        $ads = $statsService->getAdsCount();
        $bookings = $statsService->getBookingsCount();
        $comments = $statsService->getCommentsCount(); */

        $stats= $statsService->getStatistics();


        /* avant la création de service /statistic
        //les meilleurs annonces
        $bestAds= $manager->createQuery(
            'SELECT AVG(c.rating) as note, 
            a.title, a.id, u.firstname, u.lastname, u.avatar
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note DESC')->setMaxResults(5)
                                ->getResult();
            //dump($bestAds);

        //les ires annonces
        $worstAds= $manager->createQuery(
            'SELECT AVG(c.rating) as note, 
            a.title, a.id, u.firstname, u.lastname,u.avatar
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note ASC')->setMaxResults(5)
                                ->getResult();
            //dump($worstAds); 
            
            maintenant on note: */
        $bestAds= $statsService-> getAdsStats('DESC');
        $worstAds= $statsService-> getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            //deux methodes possibles:
            /*methode 1 et on utilise dans l'index {{users}}
             'users'=>$users,
            'ads'=>$ads,
            'bookings'=>$bookings,
            'comments'=>$comments */
            /*methode 2: et on utilise dans l'index {{stats.users}}*/ 
            'stats'=>$stats,
            'bestAds'=>$bestAds,
            'worstAds'=>$worstAds

        ]);
    }
}
