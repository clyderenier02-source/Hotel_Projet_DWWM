<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/user')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/promote', name: 'app_admin_user_promote', methods: ['POST'])]
    public function promote(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('promote' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        // Ajoute ROLE_ADMIN à l'utilisateur
        $user->setRoles(['ROLE_ADMIN']);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur promu admin.');

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}/demote', name: 'app_admin_user_demote', methods: ['POST'])]
    public function demote(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('demote' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_user_index');
        }
        // Retire ROLE_ADMIN, remet juste ROLE_USER
        $user->setRoles([]);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur rétrogradé.');

        return $this->redirectToRoute('app_admin_user_index');
    }
}