<?php

namespace App\Controller\admin;

use App\Entity\Opinion;
use App\Repository\OpinionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/opinion')]
final class OpinionController extends AbstractController
{
    #[Route('', name: 'app_admin_opinion_index')]
    public function index(OpinionRepository $opinionRepository): Response
    {   
        return $this->render('admin/opinion/index.html.twig', [
            'opinions' => $opinionRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'app_admin_opinion_delete', methods: ['POST'])]
    public function delete(Request $request, Opinion $opinion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($opinion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_opinion_index', [], Response::HTTP_SEE_OTHER);
    }
}
