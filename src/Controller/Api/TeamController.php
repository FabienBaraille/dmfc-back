<?php

namespace App\Controller\Api;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     * @Route("/api/teams", name="app_api_team", methods={"GET"})
     */
    public function getTeamAll(TeamRepository $teamRepository): JsonResponse
    {
        return $this->json(
            $teamRepository->findAll(),
            200,
            [],
            ['groups' => 'teams_get_collection']
        );
    }

    /**
     * Create a new team
     *
     * @Route("/api/team", name="app_api_team_create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();

        $newTeam = $serializer->deserialize($jsonContent, Team::class, 'json');

        $errors = $validator->validate($newTeam);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Vérifiez si le trigramme est déjà utilisé
        $existingTeamWithSameTrigram = $entityManager->getRepository(Team::class)->findOneBy(['trigram' => $newTeam->getTrigram()]);
        if ($existingTeamWithSameTrigram) {
            return $this->json(['errors' => ['trigram' => 'Le trigramme est déjà utilisé.']], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Vérifiez si le nom est déjà utilisé
        $existingTeamWithSameName = $entityManager->getRepository(Team::class)->findOneBy(['name' => $newTeam->getName()]);
        if ($existingTeamWithSameName) {
            return $this->json(['errors' => ['name' => 'Le nom est déjà utilisé.']], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $newTeam->setCreatedAt(new \DateTime());

        // Vérifiez si la conférence est valide
        if (!in_array($newTeam->getConference(), ['Eastern', 'Western'])) {
            return $this->json(['errors' => ['conference' => 'La conférence doit être "Eastern" ou "Western".']], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // Persistez la nouvelle équipe dans la base de données
        $entityManager->persist($newTeam);
        $entityManager->flush();

        return $this->json(
            $newTeam,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'teams_get_collection']
        );
    }


    /**
     * Update a team
     * 
     * @Route("/api/team/{id}", name="app_api_team_update", methods={"PUT"})
     */
    public function update(Request $request, Team $team, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();

        $updatedTeam = $serializer->deserialize($jsonContent, Team::class, 'json');

        $errors = $validator->validate($updatedTeam);

        // Vérifiez si le trigramme est unique
        $existingTeamWithSameTrigram = $entityManager->getRepository(Team::class)->findOneBy(['trigram' => $updatedTeam->getTrigram()]);
        if ($existingTeamWithSameTrigram && $existingTeamWithSameTrigram !== $team) {
            return $this->json(['errors' => ['trigram' => 'Le trigramme doit être unique.']], Response::HTTP_UNPROCESSABLE_ENTITY);
        }       

        // Vérifiez si le name est unique
        $existingTeamWithSameName = $entityManager->getRepository(Team::class)->findOneBy(['name' => $updatedTeam->getName()]);
        if ($existingTeamWithSameName && $existingTeamWithSameName !== $team) {
            return $this->json(['errors' => ['name' => "Le nom de l'équipe doit être unique."]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }     

        if (!in_array($updatedTeam->getConference(), ['Eastern', 'Western'])) {
            return $this->json(['errors' => ['conference' => 'La conférence doit être "Eastern" ou "Western".']], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Vérifiez si les champs sont définis avant de les mettre à jour
        $newTrigram = $updatedTeam->getTrigram();
        if ($newTrigram !== null) {
            $team->setTrigram($newTrigram);
        }

        $newName = $updatedTeam->getName();
        if ($newName !== null) {
            $team->setName($newName);
        }

        $newConference = $updatedTeam->getConference();
        if ($newConference !== null) {
            $team->setConference($newConference);
        }

        $newLogo = $updatedTeam->getLogo();
        if ($newLogo !== null) {
            $team->setLogo($newLogo);
        }

        $entityManager->flush();

        return $this->json(
            $team,
            Response::HTTP_OK,
            [],
            ['groups' => 'teams_get_collection']
        );
    }

    /**
     * Delete a team
     *
     * @Route("/api/team/{id}", name="app_api_team_delete", methods={"DELETE"})
     */
    public function delete(Team $team = null, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifiez si l'équipe existe
        if ($team === null) {
            // Réponse avec un statut 200 et le message
            return $this->json(['message' => 'L\'équipe demandée n\'existe pas.'], 200);
        }

        // Supprimez l'équipe
        $entityManager->remove($team);
        $entityManager->flush();

        // Réponse de succès
        return $this->json(['message' => 'L\'équipe a été supprimée avec succès.'], 200);
    }

}
