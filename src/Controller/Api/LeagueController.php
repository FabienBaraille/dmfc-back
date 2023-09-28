<?php

namespace App\Controller\Api;

use App\Entity\League;
use App\Repository\LeagueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeagueController extends AbstractController
{

    /**
     * GET leagues collection
     *
     * @Route("/api/leagues", name="app_api_league", methods={"GET"})
     */
    public function getLeagueAll(LeagueRepository $leagueRepository): JsonResponse
    {
        return $this->json(
            $leagueRepository->findAll(),
            200,
            [],
            ['groups' => 'leagues_get_collection']
        );
    }

    /**
    * GET league by item
    *
    * @Route("/api/leagues/{id}", name="app_api_league_id", methods={"GET"})
    */
    public function getLeagueById(LeagueRepository $leagueRepository, $id): JsonResponse
    {
        return $this->json(
            $leagueRepository->find($id),
            200,
            [],
            ['groups' => 'leagues_get_collection']
        );
    }

    /**
     * @Route("/api/leagues/{id}/users", name="app_league_id_users", methods={"GET"})
     */
    public function getUsersByLeague(LeagueRepository $leagueRepository, $id): JsonResponse
    {
        
        return $this->json(
            $leagueRepository->find($id)->getUsers(),
            200,
            [],
            ['groups' => 'leagues_get_collection']
        );
    }

    /**
     * Create League
     * 
     * @Route("/api/leagues", name="app_api_league_post", methods={"POST"})
     */
    public function postLeague(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        $league = $serializer->deserialize($jsonContent, League::class,'json');

        $errors = $validator->validate($league);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($league);
        $entityManager->flush();

        return $this->json(
            $league,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_league', ['id' => $league->getId()]),
            ],
            ['groups' => ['leagues_get_collection']]
        );
    }

    /**
     * Delete League
     *
     * @Route("/api/leagues/{id}", name="app_api_league_delete", methods={"DELETE"})
     */
    public function deleteLeague(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $league = $entityManager->getRepository(League::class)->find($id);

        if (!$league) {
            return $this->json(['message' => 'League not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($league);
        $entityManager->flush();

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
        );
    }

    /**
    * Update League
    * 
    * @Route("/api/leagues/{id}", name="app_api_league_update", methods={"PUT"})
    */
    public function updateLeague(Request $request, League $league, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();


        $updatedLeague = $serializer->deserialize($jsonContent, League::class, 'json');

        $errors = $validator->validate($updatedLeague);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Vérifiez si les champs sont définis avant de les mettre à jour
        $newLeagueName = $updatedLeague->getLeagueName();
        if ($newLeagueName !== null) {
            $league->setLeagueName($newLeagueName);
        }

        $newLeagueDescription = $updatedLeague->getLeagueDescription();
        if ($newLeagueDescription !== null) {
            $league->setLeagueDescription($newLeagueDescription);
        }

        $entityManager->persist($league);
        $entityManager->flush();

        return $this->json(
            $league,
            Response::HTTP_OK,
            [],
            ['groups' => ['leagues_get_collection']]
        );
    }
}
