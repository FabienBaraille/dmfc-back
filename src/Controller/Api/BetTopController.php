<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\BetTop;
use App\Entity\TopTen;

use App\Repository\BetTopRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BetTopController extends AbstractController
{
    /**
     * Get bet top10 made by a player
     * 
     * @Route("/api/bettop/player/{id}", name="app_api_bettop_player", methods={"GET"})
     */
    public function getBetTopByPlayer(BetTopRepository $betTopRepository, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user === null) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        $bettop = $betTopRepository->findByPlayerId($user);

        return $this->json(
            $bettop,
            200,
            [],
            ['groups' => 'betTop_get_collection']
        );
    }
    /**
     * Get bet top by Top 10 id
     * 
     * @Route("/api/bettop/topten/{id}", name="app_api_bettop_topten", methods={"GET"})
     */
    public function getBetTopByTopId(BetTopRepository $betTopRepository, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $topten = $entityManager->getRepository(TopTen::class)->find($id);

        if ($topten === null) {
            return $this->json(['message' => 'Ce top 10 n\'a pas été trouvé'], Response::HTTP_NOT_FOUND);
        }

        $bettop = $betTopRepository->findByTopten($topten);

        return $this->json(
            $bettop,
            200,
            [],
            ['groups' => 'betTop_get_collection']
        );
    }
    /**
     * Create bet top10
     * 
     * @Route("/api/bettop/new", name="app_api_bettop_create", methods={"POST"})
     */
    public function createBetTop(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        $betTopData = json_decode($jsonContent, true);

        $betTop = $serializer->deserialize($jsonContent, BetTop::class, 'json');

        if (isset($betTopData['topten'])) {
            $toptenId = $betTopData['topten'];
            $topten = $entityManager->getRepository(TopTen::class)->find($toptenId);
            if (!$topten) {
                return $this->json(['error' => 'Top 10 non trouvé.'], Response::HTTP_NOT_FOUND);
            }
            $betTop->setTopten($topten);
        }
        if (isset($betTopData['user'])) {
            $userId = $betTopData['user'];
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (!$topten) {
                return $this->json(['error' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
            }
            $betTop->setUser($user);
        }
        $betTop->setCreatedAt(new \DateTimeImmutable('now'));

        $errors = $validator->validate($betTop);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($betTop);
        $entityManager->flush();

        return $this->json(
            $betTop,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'bettop_get_post']
        );
    }
    /**
     * Update bet top10 by player
     * 
     * @Route("/api/bettop/{id}", name="app_api_bettop_update_player", methods={"PUT"})
     */
    public function updateBetTopByPlayer(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $id): JsonResponse
    {
        $betTop = $entityManager->getRepository(BetTop::class)->find($id);
        if ($betTop === null) {
            return $this->json(['message' => 'Pronostic non trouvé'], 404);
        }
        if ($betTop->getValidationStatus() === 'Validated' || $betTop->getValidationStatus() === 'Published') {
            return $this->json(['message' => 'Pronostic déjà validé ou publié'], 401);
        }

        $jsonContent = $request->getContent();
        $bettopData = json_decode($jsonContent, true);

        if (isset($bettopData['validationStatus'])) {
            $betTop->setValidationStatus($bettopData['validationStatus']);
        }
        if (isset($bettopData['predictedRanking'])) {
            $betTop->setPredictedRanking($bettopData['predictedRanking']);
        }

        $errors = $validator->validate($betTop);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($betTop);
        $entityManager->flush();

        return $this->json(
            $betTop,
            Response::HTTP_OK,
            [],
            ['groups' => 'bettop_get_post']
        );
    }
    /**
     * Update bet top10 by DMFC
     * 
     * @Route("/api/bettop/{id}/DMFC", name="app_api_bettop_update_DMFC", methods={"PUT"})
     */
    public function updateBetTopByDMFC(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $id): JsonResponse
    {
        $betTop = $entityManager->getRepository(BetTop::class)->find($id);
        if ($betTop === null) {
            return $this->json(['message' => 'Pronostic non trouvé'], 404);
        }

        $jsonContent = $request->getContent();
        $bettopData = json_decode($jsonContent, true);

        if (isset($bettopData['pointsEarned'])) {
            $betTop->setPointsEarned($bettopData['pointsEarned']);
        }

        $errors = $validator->validate($betTop);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($betTop);
        $entityManager->flush();

        return $this->json(
            ['message' => 'Points gagnés enregistrés avec succès.'],
            Response::HTTP_OK,
            [],
            ['groups' => 'bettop_get_post']
        );
    }
    /**
     * Delete bet top 10
     * 
     * @Route("/api/bettop/{id}", name="app_api_bettop_delete", methods={"DELETE"})
     */
    public function deleteBetTop(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $betTop = $entityManager->getRepository(BetTop::class)->find($id);
        if ($betTop === null) {
            return $this->json(['message' => 'Pronostic non trouvé'], 404);
        }

        $entityManager->remove($betTop);
        $entityManager->flush();

        return $this->json(['message' => 'Le pronostic a été supprimé avec succès.'], 200);
    }
}