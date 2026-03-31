<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Form\ReservationSearchType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RoomRepository;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET', 'POST'])]
    public function index(ReservationRepository $reservationRepository): Response
    {   
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy([
            'user' => $user
        ]);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    #[Route('/calendar', name: 'app_reservation_calendar', methods: ['GET', 'POST'])]
    public function detail(Request $request, ReservationRepository $reservationRepository): Response
    {
        // Création du formulaire de recherche
        $searchForm = $this->createForm(ReservationSearchType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            // Récupération des données du formulaire search (date_arrived et date_return)
            $data = $searchForm->getData();
            
            // Si les dates sont manquantes retour sur la page calendar
            if (!$data['date_arrived'] || !$data['date_return']) {

                // Message d'erreur si date manquantes
                $this->addFlash('error', 'Veuillez sélectionner les dates');

                return $this->redirectToRoute('app_reservation_calendar');
            }

            // Stockage des dates dans la session
            $session = $request->getSession();
            
            // On stock en string
            $session->set('date_arrived', $data['date_arrived']->format('d-m-Y'));
            $session->set('date_return', $data['date_return']->format('d-m-Y'));

            return $this->redirectToRoute('app_reservation_new');
        }

        return $this->render('reservation/calendar.html.twig', [
            'reservations' => $reservationRepository->findAll(),
            // Affichage de la page calendar avec le formulaire search
            'searchform' => $searchForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();

        // Récupération des dates depuis la session
        $session = $request->getSession();

        // On récupère les dates choisies depuis la page réservation calendar
        $dateArrived = $session->get('date_arrived');
        $dateReturn = $session->get('date_return');

        $dateArrived = \DateTime::createFromFormat('d-m-Y', $dateArrived);
        $dateReturn = \DateTime::createFromFormat('d-m-Y', $dateReturn);

        $dateArrived->setTime(0, 0, 0);
        $dateReturn->setTime(0, 0, 0);

        // Vérification des types Datetime
        if ($dateArrived instanceof \DateTimeInterface) {
            $reservation->setDateArrived($dateArrived);
        }

        if ($dateReturn instanceof \DateTimeInterface) {
            $reservation->setDateReturn($dateReturn);
        }

        // Si formulaire soumis et valide sauvegarde en base
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_calendar');
        }

        return $this->render('reservation/new.html.twig', [
            'rooms' => $roomRepository->findAvailableRooms($dateArrived, $dateReturn),
            'form' => $form->createView(),
            'reservation' => $reservation,
            'arrived' => $dateArrived,
            'return' => $dateReturn
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}