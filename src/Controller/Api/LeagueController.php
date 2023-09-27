<?php

namespace App\Controller\Api;

use App\Repository\LeagueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
            $leagueRepository->findAll(),
            200,
            [],
            ['groups' => 'get_login_league']
        );
    }

    /**
     * @Route("/league/{id}/users", name="app_league_id_users")
     */
    public function users(LeagueRepository $leagueRepository, $id): JsonResponse
    {
        
        return $this->json(
            $leagueRepository->find($id)->getUsers(),
            200,
            [],
            ['groups' => 'get_login_league']
        );
    }
}
