<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RoomRepository;

final class RoomController extends AbstractController
{
    #[Route('/admin/room', name: 'app_admin_room')]
    public function index(RoomRepository $roomRepository): Response
    {   
        $room = $roomRepository->findAll();

        return $this->render('admin/room/index.html.twig', [
            'rooms' => $room
        ]);
    }
}
