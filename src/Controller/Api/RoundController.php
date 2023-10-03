<?php

namespace App\Controller\Api;
use App\Entity\User;
use App\Entity\Round;
use App\Entity\League;
use App\Entity\Season;
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
     * @Route("/api/create/round", name="api_id_create_round", methods={"POST"})
     */
    public function createRound(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();
        $roundData = json_decode($jsonContent, true);

        if ($roundData === null) {
            return $this->json(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
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

        // Assurez-vous que la saison a une valeur pour la colonne 'year' avant de l'associer au tour (Round)
        // Remplacez 2023 par la valeur appropriée ou récupérez-la à partir de $roundData si elle est présente dans les données JSON.
        $season->setYear(2023);

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
        $round->setCategory('saison_reguliere');
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
}