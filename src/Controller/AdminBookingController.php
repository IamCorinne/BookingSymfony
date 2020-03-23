<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\Pagination;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Affiche la liste des réservations
     * @Route("/admin/booking/{page<\d+>?1}", name="admin_bookings_list")
     * 
     * @return Response
     */
    public function index(BookingRepository $repo,Pagination $paginationService,$page)
    {

        $paginationService  ->setEntityClass(Booking::class)
                            ->setPage($page)
                            //->setRoute('admin_bookings_list')
                            ;
        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $paginationService
        ]);
    }

    /**
     * Edition d'une réservation
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking,Request $request,EntityManagerInterface $manager)
    {
        $form= $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //modifier en même temps le montant 
            //$booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            //on va en fait forcer le montant à 0 pour le recalcule du pre enregistrement dans le callback de booking.php public function prePersist()
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash("success","La réservation a bien été modifiée");
        }

        return $this->render('admin/booking/edit.html.twig',
        [
            'booking'=>$booking, 
            'form'=>$form->createView()
        ]);
    }



    /**
     * Suppression d'une réservation
     * @Route ("/admin/booking/{id}/delete",name="admin_booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking,EntityManagerInterface $manager )
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash("success","La réservation a bien été supprimée");
        
        return $this->redirectToRoute('admin_bookings_list');
    }

    
}
