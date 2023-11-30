<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Round;
use App\Entity\Selection;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use App\Repository\RoundRepository;
use App\Repository\SrpredictionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
     * GET games by Id
     * 
     * @Route("/api/game/{id}", name="app_api_game_by_user", methods={"GET"})
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
    public function getGamesByRound(GameRepository $gameRepository, SrpredictionRepository $predictionRepository, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $round = $entityManager->getRepository(Round::class)->find($id);

        if (!$round) {
            return $this->json(['message' => 'Round non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Utilisez la méthode personnalisée de votre GameRepository pour récupérer toutes les parties d'un round
        $games = $gameRepository->findByRound($round);
        $pred = [];
        foreach ($games as $game) {
            $predictions = $predictionRepository->findBy(['Game' => $game]);
            $pred[$game->getId()] = count($predictions)  == 0;
        }

        return $this->json(
            [
                $games,
                $pred
            ],
            200,
            [],
            ['groups' => 'games_get_collection', 'games_get_round']
        );
    }

    /**
     * GET predictions by game ID
     *
     * @Route("/api/game/{id}/srprediction", name="app_api_srprediction_by_game_id", methods={"GET"})
     */
    public function getSrpredictionsByGameId(SrpredictionRepository $predictionRepository, $id): JsonResponse
    {
        // Recherchez le jeu par son ID
        $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
        if (!$game) {
            return $this->json(['error' => 'Game not found'], 404);
        }
        // Récupérez toutes les prédictions associées à ce jeu
        $predictions = $predictionRepository->findBy(['Game' => $game]);
        // Vous pouvez renvoyer les prédictions sous forme de réponse JSON
        return $this->json(
            $predictions,
            200,
            [],
            ['groups' => 'prediction']
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

        // Game Creation End
        // Update of the count of selected teams

        // Getting league Id
        $league = $game->getRound()->getLeague();

        $teamHome = $game->getTeam()[0];
        // Getting selection infos for home team
        $selectionTeamHome = $entityManager->getRepository(Selection::class)->findBy(['leagues' => $league, 'teams' => $teamHome])[0];
        if (!$selectionTeamHome) {
            return $this->json(['error' => "Ce décompte n'existe pas."], Response::HTTP_NOT_FOUND);
        }
        $selectionTeamHome->setSelectedHome($selectionTeamHome->getSelectedHome() + 1);

        $teamAway = $game->getTeam()[1];
        // Getting selection infos for visitor team
        $selectionTeamAway = $entityManager->getRepository(Selection::class)->findBy(['leagues' => $league, 'teams' => $teamAway])[0];
        if (!$selectionTeamAway) {
            return $this->json(['error' => "Ce décompte n'existe pas."], Response::HTTP_NOT_FOUND);
        }
        $selectionTeamAway->setSelectedAway($selectionTeamAway->getSelectedAway() + 1);

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
            return $this->json(['message' => 'Le match demandé n\'existe pas.'], 200);
        }

        $entityManager->remove($game);
        // Update of the count of selected teams

        // Getting league Id
        $league = $game->getRound()->getLeague();

        $teamHome = $game->getTeam()[0];
        // Getting selection infos for home team
        $selectionTeamHome = $entityManager->getRepository(Selection::class)->findBy(['leagues' => $league, 'teams' => $teamHome])[0];
        if (!$selectionTeamHome) {
            return $this->json(['error' => "Ce décompte n'existe pas."], Response::HTTP_NOT_FOUND);
        }
        $selectionTeamHome->setSelectedHome($selectionTeamHome->getSelectedHome() - 1);

        $teamAway = $game->getTeam()[1];
        // Getting selection infos for visitor team
        $selectionTeamAway = $entityManager->getRepository(Selection::class)->findBy(['leagues' => $league, 'teams' => $teamAway])[0];
        if (!$selectionTeamAway) {
            return $this->json(['error' => "Ce décompte n'existe pas."], Response::HTTP_NOT_FOUND);
        }
        $selectionTeamAway->setSelectedAway($selectionTeamAway->getSelectedAway() - 1);
        $entityManager->flush();

        // Réponse de succès
        return $this->json(['message' => 'Le match a été supprimé avec succès.'], 200);
    }

     /**
     * Update Game
     *
     * @Route("/api/game/{id}", name="app_api_game_update", methods={"PUT"})
     */
    public function updateGame(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $id): JsonResponse
    {
        $game = $entityManager->getRepository(Game::class)->find($id);
        // Vérifiez si le jeu existe
        if ($game === null) {
            // Réponse avec un statut 404 si le jeu n'est pas trouvé
            return $this->json(['message' => 'Le jeu demandé n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }
        $jsonContent = $request->getContent();
        $gameData = json_decode($jsonContent, true);
        $serializer = $this->get('serializer');
        $updatedGame = $serializer->deserialize($jsonContent, Game::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $game]);
        // Vérifiez si le champ "round" existe et si oui, mettez à jour le jeu avec le nouveau round
        if (isset($gameData['round'])) {
            $roundId = $gameData['round'];
            $round = $entityManager->getRepository(Round::class)->find($roundId);
            if (!$round) {
                return $this->json(['error' => 'Round non trouvé.'], Response::HTTP_NOT_FOUND);
            }
            $updatedGame->setRound($round);
        }
        // Vérifiez si le champ "teams" existe et si oui, mettez à jour le jeu avec les nouvelles équipes
        if (isset($gameData['teams'])) {
            $teamIds = $gameData['teams'];
            $updatedGame->clearTeams(); // Supprimer les équipes actuelles pour éviter les doublons
            foreach ($teamIds as $teamId) {
                $team = $entityManager->getRepository(Team::class)->find($teamId);
                if (!$team) {
                    return $this->json(['error' => 'Équipe non trouvée.'], Response::HTTP_NOT_FOUND);
                }
                $updatedGame->addTeam($team);
            }
        }
        // Vérifiez si le champ "homeOdd" existe et si oui, mettez à jour la cote à domicile
        if (isset($gameData['homeOdd'])) {
            $updatedGame->setHomeOdd($gameData['homeOdd']);
        }
        // Vérifiez si le champ "visitorOdd" existe et si oui, mettez à jour la cote visiteur
        if (isset($gameData['visitorOdd'])) {
            $updatedGame->setVisitorOdd($gameData['visitorOdd']);
        }
        // Calculez le gagnant en fonction des scores
        $visitorScore = $updatedGame->getVisitorScore();
        $homeScore = $updatedGame->getHomeScore();
        if (isset($gameData['winner'])) {
            $newWinner = $gameData['winner'];
            $game->setWinner($newWinner);
        }
        $updatedGame->setUpdatedAt(new \DateTime('now'));
        $errors = $validator->validate($updatedGame);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $entityManager->flush();
        return $this->json(
            ['message' => 'La modification a été effectuée avec succès.', 'game' => $updatedGame],
            Response::HTTP_OK,
            ['Location' => $this->generateUrl('app_api_game', ['id' => $updatedGame->getId()])],
            ['groups' => ['games_get_post']]
        );
    }
}
