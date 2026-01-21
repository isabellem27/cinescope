<?php

namespace App\Controller;

use App\Entity\Platforme;
use App\Form\PlatformeType;
use App\Repository\PlatformeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/platforme')]
final class PlatformeController extends AbstractController
{
    #[Route(name: 'app_platforme_index', methods: ['GET'])]
    public function index(PlatformeRepository $platformeRepository): Response
    {
        $platformes = $platformeRepository->findAll();

        return $this->render('platforme/index.html.twig', [
            'platformes' => $platformes,
        ]);
    }

    #[Route('/new', name: 'app_platforme_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $platforme = new Platforme();
        $form = $this->createForm(PlatformeType::class, $platforme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($platforme);
            $entityManager->flush();

            return $this->redirectToRoute('app_platforme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('platforme/new.html.twig', [
            'platforme' => $platforme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_platforme_show', methods: ['GET'])]
    public function show(Platforme $platforme): Response
    {
        return $this->render('platforme/show.html.twig', [
            'platforme' => $platforme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_platforme_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Platforme $platforme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlatformeType::class, $platforme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_platforme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('platforme/edit.html.twig', [
            'platforme' => $platforme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_platforme_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Platforme $platforme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$platforme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($platforme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_platforme_index', [], Response::HTTP_SEE_OTHER);
    }
}
