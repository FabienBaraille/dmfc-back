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
    * @Route("/api/league/{id}", name="app_api_league_id", methods={"GET"})
    */
    public function getLeagueById(LeagueRepository $leagueRepository, $id): JsonResponse
    {
        $league = $leagueRepository->find($id);

        if (!$league) {
            return $this->json(['message' => "Cette ligue n'existe pas"], Response::HTTP_NOT_FOUND);
        }
    
        return $this->json($league, 200, [], ['groups' => 'leagues_get_collection']);
    }

    /**
     * GET User By League
     * 
     * @Route("/api/league/{id}/users", name="app_league_id_users", methods={"GET"})
     */
    public function getUsersByLeague(LeagueRepository $leagueRepository, $id): JsonResponse
    {
        $league = $leagueRepository->find($id);

        if (!$league) {
            return $this->json(['message' => "Cette ligue n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $users = $league->getUsers();

        if (empty($users)) {
            return $this->json(['message' => "Aucun utilisateur trouvé dans cette ligue"], Response::HTTP_OK);
        }
        
        return $this->json(
            $users,
            Response::HTTP_OK,
            [],
            ['groups' => ['leagues_get_users', 'leagues_get_collection']]
        );
    }

    /**
     * @Route("/api/league/{id}/news", name="app_league_id_news", methods={"GET"})
     */
    public function getNewsByLeague(LeagueRepository $leagueRepository, $id): JsonResponse
    {
        $league = $leagueRepository->find($id);

        if (!$league) {
            return $this->json(['message' => "Cette ligue n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $news = $league->getNews();

        if (empty($news)) {
            return $this->json(['message' => "Aucune news trouvé dans cette ligue"], Response::HTTP_OK);
        }

        return $this->json(
            $news,
            Response::HTTP_OK,
            [],
            ['groups' => ['news_get_collection']]
        );
    }


    /**
     * Create League
     * 
     * @Route("/api/league/new", name="app_api_league_post", methods={"POST"})
     */
    public function postLeague(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        $league = $serializer->deserialize($jsonContent, League::class,'json');

        $league->setCreatedAt(new \DateTime('now'));

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
     * @Route("/api/league/{id}", name="app_api_league_delete", methods={"DELETE"})
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

        $league->setUpdatedAt(new \DateTime('now'));
        
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