<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Form\Admin\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/photo')]
final class PhotoController extends AbstractController
{
    #[Route(name: 'app_admin_photo_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('admin/photo/index.html.twig', [
            'photos' => $photoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération du fichier uploaddé depuis le formulaire
            $file = $form->get('filename')->getData();

            // Si un fichier a été envoyé :
            if ($file) {

            // On extracte le nom du fichier
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                // supprime les caractères spéciaux et les espaces
                // et rend le nom imprévisible pour un attaquant
                $safeFilename = $slugger->slug($originalFilename);

                // On ajoute un identifiant unique au nom du fichier pour s'assurer que deux fichiers n'aient pas le même nom
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // On déplace le fichier dans un dossier hors de la racine web
                // dossier non aaccesible dans l'URL
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                // On ajoute à notre objet
                $photo->setFilename($newFilename);
            }

            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_photo_index');
        }

        return $this->render('admin/photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_photo_show', methods: ['GET'])]
    public function show(Photo $photo): Response
    {
        return $this->render('admin/photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('filename')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $photo->setFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/photo/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($photo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_photo_index', [], Response::HTTP_SEE_OTHER);
    }
}
