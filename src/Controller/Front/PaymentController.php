<?php

namespace App\Controller\Front;

use App\Entity\Payment;
use App\Form\Front\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Reservation;
use App\Entity\Room;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/payment')]
#[IsGranted('ROLE_USER')]
final class PaymentController extends AbstractController
{
    #[Route('/{id}/pay', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();

        $dateArrived = $session->get('date_arrived');
        $dateReturn = $session->get('date_return');

        if(!$dateArrived || !$dateReturn){
            return $this->redirectToRoute('app_home');
        }

        $start = \DateTime::createFromFormat('d-m-Y', $dateArrived);
        $end = \DateTime::createFromFormat('d-m-Y', $dateReturn);

        $interval = $start->diff($end);
        $nights = $interval->days;

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        // Prix total
        $total = $nights * $room->getPriceNight();

        if($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre réservation a été prise en compte avec succès !');

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
            
            // On vide la session pour eviter une double soumission
            $session->remove('date_arrived');
            $session->remove('date_return');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('front/payment/new.html.twig', [
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
        if ($payment->getReservation()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous n\'avez pas accès à ce paiement.'
            );
        }

        return $this->render('front/payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}