<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PaymentRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/payment')]
final class PaymentController extends AbstractController
{
    #[Route('', name: 'app_admin_payment_index')]
    public function index(PaymentRepository $paymentRepository): Response
    {
        return $this->render('admin/payment/index.html.twig', [
            'payments' => $paymentRepository->findAll()
        ]);
    }
}
