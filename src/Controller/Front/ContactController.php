<?php

namespace App\Controller\Front;

use App\Entity\Contact;
use App\Form\Front\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setDateSend(new \DateTime());

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message à bien été envoyé !');

            return $this->redirectToRoute('app_contact_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
