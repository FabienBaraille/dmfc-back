<?php

namespace App\Controller\Api;

use App\Entity\TopTen;
use App\Entity\Round;
use App\Entity\Team;
use App\Repository\TopTenRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopTenController extends AbstractController
{
    /**
     * Get TopTen by Id
     *
     * @Route("/api/topten/{id}", name="app_api_topten_by_id", methods={"GET"})
     */
    public function getTopTenById(TopTenRepository $topTenRepository, $id): JsonResponse
    {
        return $this->json(
            $topten = $topTenRepository->find($id),
            200,
            [],
            ['groups' => 'topten_get_collection']
        );
    }
    /**
     * Get TopTens by round Id
     * 
     * @Route("/api/topten/round/{id}", name="app_api_topten_by_round", methods={"GET"})
     */
    public function getTopTenByRound(TopTenRepository $topTenRepository, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $round = $entityManager->getRepository(Round::class)->find($id);

        if (!$round) {
            return $this->json(['message' => 'Round non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $toptens = $topTenRepository->findByRound($round);

        return $this->json(
            $toptens,
            200,
            [],
            ['groups' => 'topten_get_collection']
        );
    }
    /**
     * Create TopTen
     * 
     * @Route("/api/topten/new", name="app_api_topten_post", methods={"POST"})
     */
    public function postTopTen(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        $toptenData = json_decode($jsonContent, true);

        $toptenEast = $serializer->deserialize($jsonContent, TopTen::class, 'json');
        $toptenWest = $serializer->deserialize($jsonContent, TopTen::class, 'json');

        if (isset($toptenData['round'])) {
            $roundId = $toptenData['round'];
            $round = $entityManager->getRepository(Round::class)->find($roundId);
            if (!$round) {
                return $this->json(['error' => 'Round non trouvé.'], Response::HTTP_NOT_FOUND);
            }
            $toptenEast->setRound($round);
            $toptenWest->setRound($round);
        }

        $teamsEast = $entityManager->getRepository(Team::class)->findTeamByConference('Eastern');
        $teamsWest = $entityManager->getRepository(Team::class)->findTeamByConference('Western');

        $toptenEast->setConference('Eastern');
        foreach ($teamsEast as $teamEast) {
            $toptenEast->addTeam($teamEast);
        }

        $errorsEast = $validator->validate($toptenEast);

        if (count($errorsEast) > 0) {
            $errorMessages = [];

            foreach ($errorsEast as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($toptenEast);
        $entityManager->flush();

        $toptenWest->setConference('Western');
        foreach ($teamsWest as $teamWest) {
            $toptenWest->addTeam($teamWest);
        }

        $errorsWest = $validator->validate($toptenWest);

        if (count($errorsWest) > 0) {
            $errorMessages = [];

            foreach ($errorsWest as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($toptenWest);
        $entityManager->flush();

        return $this->json(
            [
                $toptenEast,
                $toptenWest
            ],
            Response::HTTP_CREATED,
            [],
            ['groups' => 'topten_get_post']
        );
    }
    /**
     * Update Top ten
     * 
     * @Route("/api/topten/{id}", name="app_api_topten_update", methods={"PUT"})
     */
    public function updateTopTen(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $id): JsonResponse
    {
        $topten = $entityManager->getRepository(TopTen::class)->find($id);

        if ($topten === null) {
            return $this->json(['message' => 'Le match demandé n\'existe pas.'], 404);
        }

        $jsonContent = $request->getContent();
        $toptenData = json_decode($jsonContent, true);

        if (isset($toptenData['deadline'])) {
            $topten->setDeadline(new \DateTime($toptenData['deadline']));
        }

        if (isset($toptenData['results'])) {
            $topten->setResults($toptenData['results']);
        }

        $errors = $validator->validate($topten);
        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY]);
        }
        
        $entityManager->persist($topten);
        $entityManager->flush();

        return $this->json(
            $topten,
            Response::HTTP_OK,
            [],
            ['groups' => 'topten_get_post']
        );
    }
    /**
     * Update all Top Tens results
     * 
     * @Route("/api/topten/results/", name="app_api_toptens_results", methods={"PUT"})
     */
    public function updateAllToptensResults(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $toptensList = [];
        $data = json_decode($request->getContent(), true);
        if (isset($data['idsList'])) {
            foreach ($data['idsList'] as $key => $id) {
                $topten = $entityManager->getRepository(TopTen::class)->find($id);
                if (!$topten) {
                    return $this->json(['message' => "Ce Top 10 n'existe pas."], Response::HTTP_NOT_FOUND);
                }
                if (isset($data['results'])) {
                    $topten->setResults($data['results']);
                }
                $errors = $validator->validate($topten);
                if (count($errors) > 0) {
                    $errorMessages = [];

                    foreach ($errors as $error) {
                        $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                    }
                    return $this->json(['errors' => $errorMessages, Response::HTTP_UNPROCESSABLE_ENTITY]);
                }
                $entityManager->persist($topten);
                $entityManager->flush();
                $toptensList[$key] = $topten;
            }
            return $this->json(
                $toptensList,
                Response::HTTP_OK,
                [],
                ['groups' => 'topten_get_post']
            );
        }
    }
    /**
     * Delete Top ten
     * 
     * @Route("/api/topten/{id}", name="app_api_topten_delete", methods={"DELETE"})
     */
    public function deleteTopTen(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $topten = $entityManager->getRepository(TopTen::class)->find($id);

        if ($topten === null) {
            return $this->json(['message' => 'Le match demandé n\'existe pas.'], 404);
        }

        $entityManager->remove($topten);
        $entityManager->flush();

        return $this->json(['message' => 'Top ten a été supprimé avec succès.'], 200);
    }
}