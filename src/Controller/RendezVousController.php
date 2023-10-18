<?php

namespace App\Controller;

use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Entity\RendezVous;
use App\Form\RendezVousFormType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Symfony\Component\Clock\now;

class RendezVousController extends AbstractController
{
    #[Route('/rendez-vous', name: 'app_rdv')]
    public function index(RendezVousRepository $rdv, PersistenceManagerRegistry $doctrine): Response
    {
        $events = $rdv->findAll();

        $rdvs = [];

        foreach ($events as $event) {
            if ($event->getState() === 'Annulé') continue;
            
            if (now() > $event->getEndDatetime()) { 
                $event->setState('Passé');
                $entityManager = $doctrine->getManager();
                $entityManager->persist($event);
                $entityManager->flush();
            };

            $backgroundColor = ($event->getState() === 'Passé') ? '#D3D3D3' : '#25325b';
            $textColor = ($event->getState() === 'Passé') ? '#000' : '#fff';

            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end_datetime' => $event->getEndDatetime()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $backgroundColor,
                'borderColor' => "#03224c",
                'textColor' => $textColor,
                'url' => $this->generateUrl('app_rdv_show', ['id' => $event->getId()]),
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('rendezVous/rdv.html.twig', compact('data'));
    }

    #[Route('/rendez-vous/create', name: 'app_rdv_create')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $rendezVous = new RendezVous();

        $form = $this->createForm(RendezVousFormType::class, $rendezVous);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $rendezVous->setRdvUser($this->getUser());
            $rendezVous->setState('Validé');
            $entityManager = $doctrine->getManager();
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            return $this->redirectToRoute('app_rdv');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $validator->validate($rendezVous);

            return $this->render('rendezVous/create.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
            ]);
        }

        return $this->render('rendezVous/create.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors ?? [],
        ]);
    }

    #[Route('/rendez-vous/{id}', name: 'app_rdv_show')]
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('rendezVous/show.html.twig', [
            'rendezVous' => $rendezVous,
        ]);
    }

    #[Route('/rendez-vous/{id}/edit', name: 'app_rdv_edit')]
    public function edit(Request $request, RendezVous $rendezVous, ValidatorInterface $validator, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(RendezVousFormType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendezVous->setRdvUser($this->getUser());
            $rendezVous->setState('Validé');
            $entityManager = $doctrine->getManager();
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            return $this->redirectToRoute('app_rdv');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $validator->validate($rendezVous);

            return $this->render('rendezVous/edit.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
                'rendezVous' => $rendezVous,
            ]);
        }

        return $this->render('rendezVous/edit.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors ?? [],
            'rendezVous' => $rendezVous,
        ]);
    }

    #[Route('/rendez-vous/{id}/delete', name: 'app_rdv_delete')]
    public function delete(RendezVous $rendezVous, PersistenceManagerRegistry $doctrine): Response
    {
        if ($rendezVous->getRdvUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer ce rendez-vous');
        }

        $rendezVous->setState('Annulé');
        $entityManager = $doctrine->getManager();
        $entityManager->persist($rendezVous);
        $entityManager->flush();

        return $this->redirectToRoute('app_rdv');
    }
}
