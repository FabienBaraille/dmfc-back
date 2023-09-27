<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Repository\LeagueRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeagueController extends AbstractController
{
    /**
     * GET leagues collection
     *
     * @Route("/api/league", name="app_api_league", methods={"GET"})
     */
    public function getLeagueAll(LeagueRepository $leagueRepository): JsonResponse
    {
        return $this->json(
            $league = $leagueRepository->findAll(),
            200,
            [],
            ['groups' => 'get_login_league']
        );

    }

}