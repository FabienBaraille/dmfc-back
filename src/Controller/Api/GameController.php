<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Round;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    /**
     * GET games collection
     * 
     * @Route("/api/games", name="app_api_game", methods={"GET"})
     */
    public function getGamesAll(GameRepository $gameRepository): JsonResponse
    {
        return $this->json(
            $gameRepository->findAll(),
            200,
            [],
            ['groups' => 'games_get_collection']
        );
    }

    /**
     * GET games by user
     * 
     * @Route("/api/games/user/{id}", name="app_api_game_by_user", methods={"GET"})
     */
    public function getGamesByUser(GameRepository $gameRepository, $id): JsonResponse
    {
        return $this->json(
            $gameRepository->find($id),
            200,
            [],
            ['groups' => 'games_get_collection']
        );
    }

    /**
     * GET games by round
     * 
     * @Route("/api/games/round/{id}", name="app_api_game_by_round", methods={"GET"})
     */
    public function getGamesByRound(GameRepository $gameRepository, $id): JsonResponse
    {
        return $this->json(
            $gameRepository->find($id),
            200,
            [],
            ['groups' => 'games_get_collection', 'games_get_round']
        );
    }

/**
 * Create Game
 * 
 * @Route("/api/game/new", name="app_api_game_post", methods={"POST"})
 */
public function postGame(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
{
    $jsonContent = $request->getContent();
    $gameData = json_decode($jsonContent, true);

    $game = $serializer->deserialize($jsonContent, Game::class,'json');

    // Vérifiez si le champ "round" existe et si oui, associez le match à un round
    if (isset($gameData['round'])) {
        $roundId = $gameData['round'];
        $round = $entityManager->getRepository(Round::class)->find($roundId);
        if (!$round) {
            return $this->json(['error' => 'Round non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        $game->setRound($round);
    }

    // Vérifiez si le champ "teams" existe et si oui, associez le match à des équipes
    if (isset($gameData['teams'])) {
        $teamIds = $gameData['teams'];
        foreach ($teamIds as $teamId) {
            $team = $entityManager->getRepository(Team::class)->find($teamId);
            if (!$team) {
                return $this->json(['error' => 'Équipe non trouvée.'], Response::HTTP_NOT_FOUND);
            }
            $game->addTeam($team);
        }
    }

    $game->setCreatedAt(new \DateTime('now'));

    $errors = $validator->validate($game);

    if (count($errors) > 0) {
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $entityManager->persist($game);
    $entityManager->flush();

    return $this->json(
        $game,
        Response::HTTP_CREATED,
        [
            'Location' => $this->generateUrl('app_api_game', ['id' => $game->getId()]),
        ],
        ['groups' => ['games_get_post']]
    );
}

    /**
     * Delete Game
     *
     * @Route("/api/game/{id}", name="app_api_game_delete", methods={"DELETE"})
     */
    public function deleteGame(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $game = $entityManager->getRepository(Game::class)->find($id);

        // Vérifiez si la game existe
        if ($game === null) {
            // Réponse avec un statut 200 et le message
            return $this->json(['message' => 'La game demandée n\'existe pas.'], 200);
        }

        $entityManager->remove($game);
        $entityManager->flush();

        // Réponse de succès
        return $this->json(['message' => 'La game a été supprimée avec succès.'], 200);
    }

    /**
    * Update Game
    * 
    * @Route("/api/game/{id}", name="app_api_game_update", methods={"PUT"})
    */
    public function updateGame(Request $request, Game $game, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();


        $updatedLeague = $serializer->deserialize($jsonContent, Game::class, 'json');

        $errors = $validator->validate($updatedLeague);

        $game->setUpdatedAt(new \DateTime('now'));
        
        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Vérifiez si les champs sont définis avant de les mettre à jour
        $dateAndTimeOfMatch = $updatedLeague->setDateAndTimeOfMatch();
        if ($dateAndTimeOfMatch !== null) {
            $game->setDateAndTimeOfMatch($dateAndTimeOfMatch);
        }

        $visitorScore = $updatedLeague->getVisitorScore();
        if ($visitorScore !== null) {
            $game->setVisitorScore($visitorScore);
        }

        $homeScore = $updatedLeague->getHomeScore();
        if ($homeScore !== null) {
            $game->setHomeScore($homeScore);
        }

        $winner = $updatedLeague->getWinner();
        if ($winner !== null) {
            $game->setWinner($winner);
        }

        $visitorOdd = $updatedLeague->getVisitorOdd();
        if ($visitorOdd !== null) {
            $game->setVisitorOdd($visitorOdd);
        }

        $homeOdd = $updatedLeague->getHomeOdd();
        if ($homeOdd !== null) {
            $game->setHomeOdd($homeOdd);
        }

        $entityManager->persist($game);
        $entityManager->flush();

        return $this->json(
            $game,
            Response::HTTP_OK,
            [],
            ['groups' => ['games_get_collection']]
        );
    }
}
