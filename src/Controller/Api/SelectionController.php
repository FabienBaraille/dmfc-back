<?php

namespace App\Controller\Api;

use App\Entity\Team;
use App\Entity\League;
use App\Entity\Selection;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LeagueRepository;
use App\Repository\SelectionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SelectionController extends AbstractController
{
  /**
   * GET selections by league Id
   * @Route("/api/selections/{id}", name="app_api_selections", methods={"GET"})
   */
  public function getSelections(LeagueRepository $leagueRepository, SelectionRepository $selectionRepository, $id): JsonResponse
  {
    $league = $leagueRepository->find($id);
    if (!$league) {
      return $this->json(['error' => "Cette ligue n'existe pas"], 404);
    }
    $selections = $selectionRepository->findBy(['leagues' => $league]);

    return $this->json(
      $selections,
      200,
      [],
      ['groups' => 'selections_get_collection']
    );
  }
}