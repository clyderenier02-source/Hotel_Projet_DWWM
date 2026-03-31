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
use App\Entity\Reservation;
use App\Entity\Room;

#[Route('/payment')]
final class PaymentController extends AbstractController
{
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

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        // Prix total
        $total = $nights * $room->getPriceNight();

        if($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre réservations a été pris en compte avec succes');

            $mode = $form->get('mode')->getData();

            $status = 'paid';

            $reservation = new Reservation();
            $reservation->setUser($this->getUser());
            $reservation->setRoom($room);
            $reservation->setDateArrived($start);
            $reservation->setDateReturn($end);
            $reservation->setTotalPrice($total);
            $reservation->setStatus($status);

            $payment = new Payment();
            $payment->setStatus($status);
            $payment->setDatePayment(new \DateTime());
            $payment->setTotal($total);
            $payment->setMode($mode);
            $payment->setReservation($reservation);

            $entityManager->persist($reservation);
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app_account');
        }

        return $this->render('payment/new.html.twig', [
            'form' => $form->createView(),
            'arrived' => $dateArrived,
            'return' => $dateReturn,
            'room' => $room,
            'price' => $total
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}
