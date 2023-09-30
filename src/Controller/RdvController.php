<?php

namespace App\Controller;

use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RdvController extends AbstractController
{
    #[Route('/rendez-vous', name: 'app_rdv')]
    public function index(RendezVousRepository $rdv): Response
    {
        $events = $rdv->findAll();

        $rdvs = [];

        foreach ($events as $event) {
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
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('rdv.html.twig', compact('data'));
    }
}
