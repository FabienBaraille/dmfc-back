<?php

namespace App\Controller;

use App\Entity\Game;
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
            ['groups' => 'games_get_user']
        );
    }

    /**
     * GET games by user
     * 
     * @Route("/api/games/round/{id}", name="app_api_game_by_round", methods={"GET"})
     */
    public function getGamesByRound(GameRepository $gameRepository, $id): JsonResponse
    {
        return $this->json(
            $gameRepository->find($id),
            200,
            [],
            ['groups' => 'games_get_round']
        );
    }

    /**
     * Create Game
     * 
     * @Route("/api/games/new", name="app_api_game_post", methods={"POST"})
     */
    public function postGame(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        $game = $serializer->deserialize($jsonContent, Game::class,'json');

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
            ['groups' => ['games_get_collection']]
        );
    }

    /**
     * Delete Game
     *
     * @Route("/api/games/{id}", name="app_api_game_delete", methods={"DELETE"})
     */
    public function deleteGame(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $game = $entityManager->getRepository(Game::class)->find($id);

        if (!$game) {
            return $this->json(['message' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($game);
        $entityManager->flush();

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
        );
    }
}
