<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Srprediction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SrpredictionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SrpredictionController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

   /**
    * GET prediction by user
    *
    * @Route("/api/srprediction/{id}", name="app_api_srprediction_user_by_id", methods={"GET"})
    */
    public function getSrpredictionsByUserId(UserRepository $userRepository, SrpredictionRepository $predictionRepository, $id): JsonResponse
    {
    // Recherchez l'utilisateur par ID
    $user = $userRepository->find($id);

    if (!$user) {
        return $this->json(['error' => 'User not found'], 404);
    }

    // Récupérez toutes les prédictions associées à l'utilisateur
    $predictions = $predictionRepository->findBy(['User' => $user]);

    // Vous pouvez renvoyer les prédictions sous forme de réponse JSON
    return $this->json(
        $predictions,
        200,
        [],
        ['groups' => 'prediction']
    );
}
    /**
     * POST prediction by user
     *
     * @Route("/api/srprediction/new", name="app_api_srprediction_new", methods={"POST"})
     */
    public function newSrPrediction(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();

        // Désérialisez les données JSON en un objet
        $srprediction = $serializer->deserialize($jsonData, Srprediction::class, 'json');

        $User = $this->security->getUser(); // Obtenez l'utilisateur actuellement authentifié

        // Vérifiez si l'utilisateur est authentifié
        if (!$User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour faire votre pronostic.');
        }

        // Récupérez l'ID de la partie à partir des données JSON
        $data = json_decode($jsonData, true);
        $gameId = $data['gameId'];

        // Vérifiez si l'ID de la partie est valide (vous pouvez ajouter des validations supplémentaires si nécessaire)
        if ($gameId !== null && is_numeric($gameId)) {
            // Recherchez si l'utilisateur a déjà fait un pronostic pour cette partie
            $existingPrediction = $entityManager->getRepository(Srprediction::class)->findOneBy(['User' => $User, 'Game' => $gameId]);

            if ($existingPrediction) {
                // Si un pronostic existe déjà, renvoyez un message d'erreur personnalisé
                return new JsonResponse(['message' => 'Vous avez déjà fait un pronostic pour cette partie. Vous ne pouvez pas refaire de pronostic.'], 400);
            } else {
                // Associez l'ID de la partie à la prédiction
                $Game = $this->getDoctrine()->getRepository(Game::class)->find($gameId);
                if ($Game) {
                    $srprediction->setGame($Game);
                } else {
                    return new JsonResponse(['message' => 'ID de partie invalide'], 400);
                }
            }
        } else {
            return new JsonResponse(['message' => 'ID de partie manquant ou invalide'], 400);
        }

        // Vérifiez les autres conditions de création, y compris validation_status
        if (
            $srprediction instanceof Srprediction &&
            $srprediction->getPredictedWinnigTeam() !== null &&
            $srprediction->getPredictedPointDifference() !== null &&
            $srprediction->getPointScored() !== null &&
            $srprediction->getBonusBookie() !== null &&
            $srprediction->getBonusPointsErned() !== null &&
            in_array($srprediction->getValidationStatus(), ['Validated', 'Saved', 'Published']) // Vérifiez la validité de validation_status
        ) {
            $srprediction->setUser($User); // Associez l'utilisateur actuellement authentifié à la prédiction
            $srprediction->setCreatedAt(new \DateTime('now'));

            // Persistez la nouvelle prédiction dans la base de données
            $entityManager->persist($srprediction);
            $entityManager->flush();

            $jsonData = $serializer->serialize($srprediction, 'json', ['groups' => 'prediction']);
            return new JsonResponse(['message' => 'Votre prédiction a été créée avec succès', 'prediction' => json_decode($jsonData)]);
        } else {
            // Si les conditions ne sont pas remplies, renvoyez une réponse JSON d'erreur
            return new JsonResponse(['message' => 'Conditions de création non remplies'], 400);
        }
    }
    
    /**
    * POST prediction by user update
    * 
    * @Route("/api/prediction/update/{id}", name="update_prediction", methods={"PUT"})
    */
    public function updateSrPrediction(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, Srprediction $srprediction): JsonResponse
    {
        $jsonData = $request->getContent();
        // Vérifiez si l'utilisateur est authentifié
        $User = $this->security->getUser();
        if (!$User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre pronostic.');
        }
        // Vérifiez si l'utilisateur a le droit de modifier la prédiction
        if ($srprediction->getUser() !== $User) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette prédiction.');
        }
        // Vérifiez si la condition est "Validated"
        if ($srprediction->getValidationStatus() === 'Validated') {
            return new JsonResponse(['message' => 'La prédiction est déjà validée. Vous ne pouvez pas la modifier.'], 400);
        }
        // Désérialisez les données JSON en un objet
        $updatedPrediction = $serializer->deserialize($jsonData, Srprediction::class, 'json');
        // Mettez à jour les champs de la prédiction existante avec les nouvelles données
        $srprediction->setPredictedWinnigTeam($updatedPrediction->getPredictedWinnigTeam());
        $srprediction->setPredictedPointDifference($updatedPrediction->getPredictedPointDifference());
        $srprediction->setValidationStatus($updatedPrediction->getValidationStatus());
        $srprediction->setUpdatedAt(new \DateTime('now'));
        // Persistez les modifications dans la base de données
        $entityManager->flush();
        $jsonData = $serializer->serialize($srprediction, 'json', ['groups' => 'prediction']);
        return new JsonResponse(['message' => 'Votre pronostic a été mis à jour avec succès', 'prediction' => json_decode($jsonData)]);
    }

    /**
     * GET predictions by game ID
     *
     * @Route("/api/srprediction/game/{gameId}", name="app_api_srprediction_by_game_id", methods={"GET"})
     */
    public function getSrpredictionsByGameId(SrpredictionRepository $predictionRepository, $gameId): JsonResponse
    {
        // Recherchez le jeu par son ID
        $game = $this->getDoctrine()->getRepository(Game::class)->find($gameId);

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

}


