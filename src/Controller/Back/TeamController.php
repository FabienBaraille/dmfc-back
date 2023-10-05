<?php

namespace App\Controller\Back;

use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     * @Route("/back/team", name="app_back_team")
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('back/team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }
}
