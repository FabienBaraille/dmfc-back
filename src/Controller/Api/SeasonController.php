<?php

namespace App\Controller\Api;

use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SeasonController extends AbstractController
{
    /**
     * GET all seasons
     * 
     * @Route("/api/seasons", name="app_api_season_get_all", methods={"GET"})
     */
    public function getAllSeasons(SeasonRepository $seasonRepository): JsonResponse
    {
        return $this->json(
            $seasonRepository->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'seasons_get_collection']
        );
    }

    /**
     * GET season by ID
     * 
     * @Route("/api/season/{id}", name="app_api_season_get_by_id", methods={"GET"})
     */
    public function getSeasonById(Season $season): JsonResponse
    {
        return $this->json(
            $season,
            Response::HTTP_OK,
            [],
            ['groups' => 'seasons_get_collection']
        );
    }

    /**
     * Create a new season
     * 
     * @Route("/api/seasons", name="app_api_season_create", methods={"POST"})
     */
    public function createSeason(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();

        $season = $serializer->deserialize($jsonContent, Season::class, 'json');

        $season->setCreatedAt(new \DateTime('now'));

        $errors = $validator->validate($season);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($season);
        $entityManager->flush();

        return $this->json(
            $season,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_season_get_by_id', ['id' => $season->getId()]),
            ],
            ['groups' => 'seasons_get_collection']
        );
    }

    /**
     * Update a season
     * 
     * @Route("/api/seasons/{id}", name="app_api_season_update", methods={"PUT"})
     */
    public function updateSeason(Request $request, Season $season, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();

        $updatedSeason = $serializer->deserialize($jsonContent, Season::class, 'json');

        $errors = $validator->validate($updatedSeason);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Mise à jour des propriétés de la saison en fonction des nouvelles valeurs
        $newYear = $updatedSeason->getYear();
        if ($newYear !== null) {
            $season->setYear($newYear);
        }

        $entityManager->flush();

        return $this->json(
            $season,
            Response::HTTP_OK,
            [],
            ['groups' => 'seasons_get_collection']
        );
    }

    /**
     * Delete a season
     * 
     * @Route("/api/seasons/{id}", name="app_api_season_delete", methods={"DELETE"})
     */
    public function delete(Season $season = null, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifiez si la saison existe
        if ($season === null) {
            // Réponse avec un statut 200 et le message
            return $this->json(['message' => 'La saison demandée n\'existe pas.'], 200);
        }

        // Supprimez la saison
        $entityManager->remove($season);
        $entityManager->flush();

        // Réponse de succès
        return $this->json(['message' => 'La saison a été supprimée avec succès.'], 200);
    }
}
