<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RoomRepository;
use App\Entity\Reservation;
use App\Entity\Room;

#[Route('/payment')]
final class PaymentController extends AbstractController
{
    #[Route('/{id}', name: 'app_payment_index', methods: ['GET', 'POST'])]
    public function index(int $id, Request $request, RoomRepository $roomRepository, PaymentRepository $paymentRepository, EntityManagerInterface $entityManager): Response
    {   
        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        // On récupère l'id de la room
        $room = $roomRepository->find($id);

        // Récupération de la session de l'utilisateur
        $session = $request->getSession();

        // Récupération des date depuis la session
        $dateArrived = $session->get('date_arrived');
        $dateReturn = $session->get('date_return');

        // On initialise a 0 la variables pour stocker le prix total
        $total = 0;
        
        // Vérifie que la date arrivé date retour et la chambre existe
        if($dateArrived && $dateReturn && $room) {

            // Crée des objets DateTime a partir des dates fournies
            $start = new \DateTime($dateArrived);
            $end = new \DateTime($dateReturn);

            // Calcul la difference entre les deux dates
            $interval = $start->diff($end);

            // Récupère le nombre total de jours (nuits)
            $nights = $interval->days;

            // Calcul le prix total nombre de nuits x prix par nuit de la chambre
            $total = $nights * $room->getPriceNight();
        }

        if($form->isSubmitted() && $form->isValid()) {
            
            $payment = $form->getData();
            $payment->setTotal($total);

            $entityManager->persist($payment);
            $entityManager->flush();
        }

        return $this->render('payment/index.html.twig', [
            'form' => $form->createView(),
            'arrived' => $dateArrived,
            'return' => $dateReturn,
            'room' => $room,
            'price' => $total
        ]);
    }

    #[Route('/{id}/pay', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {   
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();

        $dateArrived = $session->get('date_arrived');
        $dateReturn = $session->get('date_return');

        if(!$dateArrived || !$dateReturn){
            return $this->redirectToRoute('app_home');
        }

        $start = new \DateTime($dateArrived);
        $end = new \DateTime($dateReturn);

        $interval = $start->diff($end);
        $nights = $interval->days;

        if($nights <= 0){
            throw new \Exception("Dates invalides");
        }

        // Prix total
        $total = $nights * $room->getPriceNight();

        // Reservation
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setRoom($room);
        $reservation->setDateArrived($start);
        $reservation->setDateReturn($end);
        $reservation->setTotalPrice($total);
        $reservation->setStatus('paid');

        // Payment
        $payment = new Payment();
        $payment->setStatus('paid');
        $payment->setDatePayment(new \DateTime());
        $payment->setTotal($total);
        $payment->setMode("card"); // ULTRA IMPORTANT
        $payment->setReservation($reservation);

        $entityManager->persist($reservation);
        $entityManager->persist($payment);
        $entityManager->flush();

        return $this->redirectToRoute('app_account');
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
