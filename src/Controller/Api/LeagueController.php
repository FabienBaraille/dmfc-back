<?php

namespace App\Controller\Api;

use App\Repository\LeagueRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeagueController extends AbstractController
{
    /**
     * GET league collection
     * 
     * @Route("/api/league/{id}/users", name="app_api_league_by_id", methods={"GET"})
     */
    public function LeagueId(LeagueRepository $leagueRepository, $id ): JsonResponse

    {
        $league = $leagueRepository->find($id);
        return $this->json(["user" => $league -> getUsers()]);
    }
}
