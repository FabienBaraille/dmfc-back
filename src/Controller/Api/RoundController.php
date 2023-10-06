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
  * @Route("/api/round/new", name="api_id_create_round", methods={"POST"})
  */
  public function createRound(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
  {
    $jsonContent = $request->getContent();
    $roundData = json_decode($jsonContent, true);

    if ($roundData === null) {
      return $this->json(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
    }

    // Vérifiez si "season" existe dans les données JSON
    if (!isset($roundData['season'])) {
      return $this->json(['error' => 'La clé "season" est manquante dans les données JSON'], Response::HTTP_BAD_REQUEST);
    }

    // Autres vérifications pour s'assurer que d'autres données requises existent
    if (!isset($roundData['user_id'])) {
      return $this->json(['error' => 'La clé "user_id" est manquante dans les données JSON'], Response::HTTP_BAD_REQUEST);
    }

    if (!isset($roundData['league_id'])) {
      return $this->json(['error' => 'La clé "league_id" est manquante dans les données JSON'], Response::HTTP_BAD_REQUEST);
    }

    // Désérialisez les données JSON dans une nouvelle entité Round
    $round = $serializer->deserialize($jsonContent, Round::class, 'json');

    // Récupérez l'ID de la saison depuis les données JSON
    $seasonId = $roundData['season']['id'];

    // Chargez l'entité Season (Saison) correspondante depuis la base de données en utilisant l'identifiant
    $season = $entityManager->getRepository(Season::class)->find($seasonId);

    if (!$season) {
      return $this->json(['error' => 'Saison introuvable'], Response::HTTP_NOT_FOUND);
    }

    // Récupérez l'année depuis les données JSON et associez-la à la saison (Season)
    /*$year = $roundData['season']['year'];
    $season->setYear($year);*/

    // Associez la saison au tour (Round)
    $round->setSeason($season);

    // Associez la ligue au tour en utilisant league_id depuis les données JSON
    $leagueId = $roundData['league_id'];
    $league = $entityManager->getRepository(League::class)->find($leagueId);

    if (!$league) {
      return $this->json(['error' => 'Ligue introuvable'], Response::HTTP_NOT_FOUND);
    }

    $round->setLeague($league);

    // Associez l'utilisateur (User) au tour en utilisant user_id depuis les données JSON
    $userId = $roundData['user_id'];
    $user = $entityManager->getRepository(User::class)->find($userId);

    if (!$user) {
      return $this->json(['error' => 'Utilisateur introuvable'], Response::HTTP_NOT_FOUND);
    }

    $round->setUser($user);

    // Ajoutez d'autres propriétés au tour (Round) si nécessaire
    $round->setCategory('saison_Reguliere');
    $round->setCreatedAt(new \DateTime('now'));

    // Validez l'entité Round
    $errors = $validator->validate($round);

    if (count($errors) > 0) {
      $errorMessages = [];

      foreach ($errors as $error) {
        $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
      }
      
      return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Persistez et flush (enregistrez) le tour dans la base de données
    $entityManager->persist($round);
    $entityManager->flush();

    return $this->json(
    $round,
    Response::HTTP_CREATED,
    [
    'Location' => $this->generateUrl('api_id_create_round', ['id' => $round->getId()]),
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