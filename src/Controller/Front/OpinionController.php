<?php

namespace App\Controller\Front;

use App\Entity\Opinion;
use App\Repository\ReservationRepository;
use App\Form\Front\OpinionType;
use App\Repository\OpinionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/opinion')]
final class OpinionController extends AbstractController
{
    #[Route(name: 'app_opinion_index', methods: ['GET'])]
    public function index(OpinionRepository $opinionRepository): Response
    {
        return $this->render('front/opinion/index.html.twig', [
            'opinions' => $opinionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_opinion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReservationRepository $reservationRepo, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();
        $reservation = $reservationRepo->findActiveReservationForUser($user);

        $opinion = new Opinion();
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if (!$reservation || $reservation->getOpinion() !== null) {

            $this->addFlash('error', 'Vous devez être en séjour et ne pas avoir déjà laissé un avis.');

            return $this->redirectToRoute('app_opinion_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre avis a bien été pris en compte merci beaucoup !');

            $opinion->setDateSend( new \DateTime());
            $opinion->setReservation($reservation);
            
            $entityManager->persist($opinion);
            $entityManager->flush();

            return $this->redirectToRoute('app_opinion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/opinion/new.html.twig', [
            'opinion' => $opinion,
            'form' => $form,
        ]);
    }
}
