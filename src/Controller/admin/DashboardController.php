<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard_index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [

        ]);
    }
}
