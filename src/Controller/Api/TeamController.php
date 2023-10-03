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
     * Update a team
     * 
     * @Route("/api/team/{id}", name="app_api_team_update", methods={"PUT"})
     */
    public function update(Request $request, Team $team, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();

        $updatedTeam = $serializer->deserialize($jsonContent, Team::class, 'json');

        $errors = $validator->validate($updatedTeam);

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
