<?php

namespace App\Controller\Api;
use App\Entity\User;
use App\Entity\Round;
use App\Entity\League;
use App\Entity\Season;
use App\Repository\RoundRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoundController extends AbstractController
{

  /**
   * Create Round
   * 
  * @Route("/api/round/new", name="app_api_round_post", methods={"POST"})
  */
  public function postRound(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        $roundData = json_decode($jsonContent, true);

        $round = $serializer->deserialize($jsonContent, Round::class,'json');

        // Vérifiez si le champ "season" existe et si oui, associez le round à une saison
        if (isset($roundData['season'])) {
            $seasonId = $roundData['season'];
            $season = $entityManager->getRepository(Season::class)->find($seasonId);
            if (!$season) {
                return $this->json(['error' => 'Saison non trouvée.'], Response::HTTP_NOT_FOUND);
            }
            $round->setSeason($season);
        } 

        // Vérifiez si le champ "league" existe et si oui, associez le round à une league
        if (isset($roundData['league'])) {
          $leagueId = $roundData['league'];
          $league = $entityManager->getRepository(League::class)->find($leagueId);
          if (!$league) {
              return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
          }
          $round->setleague($league);
      }
      
        // Vérifiez si le champ "user" existe et si oui, associez le round à un utilisateur
        if (isset($roundData['user'])) {
          $userId = $roundData['user'];
          $user = $entityManager->getRepository(User::class)->find($userId);
          if (!$user) {
              return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
          }
          $round->setuser($user);
      }

        $round->setCreatedAt(new \DateTime('now'));

        $errors = $validator->validate($round);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($round);
        $entityManager->flush();

        return $this->json(
            $round,
            Response::HTTP_CREATED,
            [
                // 'Location' => $this->generateUrl('app_api_round', ['id' => $round->getId()]),
            ],
            ['groups' => ['rounds_get_collection']]
        );
    }

  
  /**
  * Updated Round
  *
  * @Route("/api/round/{id}", name="api_id_update_round", methods={"PUT"})
  */
  public function updateRound(Request $request, $id, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
  {
    // Récupérez l'entité Round existante en utilisant son identifiant ($id)
    $round = $entityManager->getRepository(Round::class)->find($id);

    if (!$round) {
      return $this->json(['error' => 'Round introuvable'], Response::HTTP_NOT_FOUND);
    }

    // Parsez les données JSON de la requête
    $jsonContent = $request->getContent();
    $roundData = json_decode($jsonContent, true);

    if ($roundData === null) {
      return $this->json(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
    }

    // Vous pouvez mettre à jour les propriétés du tour existant avec les données JSON reçues
    // Par exemple, pour mettre à jour le nom et la catégorie :
    if (isset($roundData['name'])) {
      $round->setName($roundData['name']);
    }
    if (isset($roundData['category'])) {
      $round->setCategory($roundData['category']);
    }
    
    // Validez l'entité Round mise à jour
    $errors = $validator->validate($round);

    if (count($errors) > 0) {
      $errorMessages = [];

    foreach ($errors as $error) {
      $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
    }

    return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Persistez les modifications dans la base de données
    $entityManager->flush();

    return $this->json(
    $round,
    Response::HTTP_OK,
    [
    'Location' => $this->generateUrl('api_id_update_round', ['id' => $round->getId()]),
    ],
    ['groups' => ['rounds_get_collection']]
    );
  }
  
  /**
  * @Route("/api/round/{id}", name="api_id_delete_round", methods={"DELETE"})
  */
  public function deleteRound($id, EntityManagerInterface $entityManager): JsonResponse
  {
    // Récupérez l'entité Round à supprimer en utilisant son identifiant ($id)
    $round = $entityManager->getRepository(Round::class)->find($id);

    if (!$round) {
      return $this->json(['error' => 'Round introuvable'], Response::HTTP_NOT_FOUND);
    }
    // Supprimez l'entité Round de la base de données
    $entityManager->remove($round);
    $entityManager->flush();

    return $this->json(['message' => 'Round supprimé avec succès'], Response::HTTP_OK);
  }
}