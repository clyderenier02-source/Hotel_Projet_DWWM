<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RGPDController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('front/rgpd/mentions-legales.html.twig');
    }

    #[Route('/politique', name: 'app_politique')]
    public function politique(): Response
    {
        return $this->render('front/rgpd/politique.html.twig');
    }
}
