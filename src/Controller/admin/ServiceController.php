<?php

namespace App\Controller\admin;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController
{
    #[Route('/admin/service', name: 'app_admin_service')]
    public function index(ServiceRepository $serviceRepository): Response
    {   
        $service = $serviceRepository->findAll();

        return $this->render('admin/service/index.html.twig', [
            'services' => $service
        ]);
    }
}
