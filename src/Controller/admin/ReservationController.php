<?php

namespace App\Controller\admin;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/reservation')]
final class ReservationController extends AbstractController
{
    #[Route('', name: 'app_admin_reservation_index')]
    public function index(ReservationRepository $reservationRepository): Response
    {   
        $reservation = $reservationRepository->findAll();

        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $reservation
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}
