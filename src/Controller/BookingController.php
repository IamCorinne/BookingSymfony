<?php

namespace App\Controller;



use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * permet d'afficher le formulaire de réservation
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function book(Ad $ad,Request $request,EntityManagerInterface $manager)
    {
        //on instancie la réservation
        $booking = new Booking();
        $form= $this->createForm(BookingType::class, $booking);

        //validation du form par bouton
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad)
                    ;
            //si les dates ne sont pas disponibles
            if(!$booking->isBookableDays())
            {
                $this->addFlash("warning","Ces dates ne sont pas disponibles. Choississez une autre date.");
            }
            else
            {
            $manager->persist($booking);
            $manager->flush();
            //on redirige avec en premier pramètre  l'ID qui apparaitra dans le chemin et en second paramètre le nom de l'alerte créé dans template/booking/show.thml.twig (true =1)
            return $this->redirectToRoute("booking_show",["id"=>$booking->getId(),'alert'=>true]);
            }
        }

        //on retourne la page
        return $this->render('booking/book.html.twig', [
            'ad'=>$ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche une réservation
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking, Request $request,EntityManagerInterface $manager )
    {
        $comment = new Comment();
        $form=$this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());
                    
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash("succes","Votre commentaire est bien enregistré. Merci.");
        }
        return $this->render('booking/show.html.twig',['booking'=>$booking,'form'=>$form->createView()]);
    }
}
