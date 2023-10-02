<?php

namespace App\Controller\Api;

use id;
use DateTime;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\League;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    /**
    * GET users collection
    *
    * @Route("/api/users", name="app_api_user", methods={"GET"})
    */
    public function getUserAll(UserRepository $userRepository): JsonResponse
    {
        return $this->json(
            $userRepository->findAll(),
            200,
            [],
            ['groups' => 'user_get_collection']
        );
    }

    /**
    * GET users by item
    *
    * @Route("/api/user/{id}", name="app_api_user_by_id", methods={"GET"})
    */
    public function getUserById(UserRepository $userRepository, $id): JsonResponse
    {
        return $this->json(
            $userRepository->find($id),
            200,
            [],
            ['groups' => 'user_get_item']
        );
    }

  /**
   * Create User
   *
   * @Route("/api/user/new", name="app_api_user_post", methods={"POST"})
   */
  public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
{
    $jsonContent = $request->getContent();
    $userData = json_decode($jsonContent, true);

    // Vérifiez si le nom d'utilisateur ou l'email est déjà utilisé
    $existingUser = $userRepository->findOneBy(['username' => $userData['username']]);
    if ($existingUser) {
        return $this->json(['error' => 'Nom d\'utilisateur déjà utilisé.'], Response::HTTP_CONFLICT);
    }

    $existingEmail = $userRepository->findOneBy(['email' => $userData['email']]);
    if ($existingEmail) {
        return $this->json(['error' => 'Email déjà utilisé.'], Response::HTTP_CONFLICT);
    }

    $user = $serializer->deserialize($jsonContent, User::class, 'json');

    // Vérifiez si le champ "league" existe et si oui, associez l'utilisateur à une ligue
    if (isset($userData['league'])) {
        $leagueId = $userData['league'];
        $league = $entityManager->getRepository(League::class)->find($leagueId);
        if (!$league) {
            return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
        }
        $user->setLeague($league);
    }    

    // Hasher le mot de passe avant la sauvegarde
    $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
    $user->setPassword($hashedPassword);

    $errors = $validator->validate($user);

    $user->setCreatedAt(new \DateTime('now'));

    if (count($errors) > 0) {
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
        }            
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $entityManager->persist($user);
    $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_user', ['id' => $user->getId()]),
            ],
            ['groups' => ['user_get_item']]
        );
}


      /**
      * Delete User
      * 
      * @Route("/api/user/{id}", name="app_api_user_delete", methods={"DELETE"})
      */
      public function deleteUser(EntityManagerInterface $entityManager, $id): JsonResponse
      {
          $user = $entityManager->getRepository(User::class)->find($id);

          if (!$user) {
              return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
          }

          $entityManager->remove($user);
          $entityManager->flush();

          return $this->json(null, Response::HTTP_NO_CONTENT);
      }

        /**
         * Update User
         *
         * @Route("/api/user/{id}", name="app_api_user_update", methods={"PUT"})
         */
        public function updateUser(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $id, UserPasswordHasherInterface $passwordHasher): JsonResponse
        {
            // Récupérez l'utilisateur existant depuis la base de données
            $user = $entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Désérialisez les données JSON de la requête en un objet User
            $jsonContent = $request->getContent();
            $userData = json_decode($jsonContent, true);

            // Mise à jour des champs de l'utilisateur
            if (isset($userData['username'])) {
                $user->setUsername($userData['username']);
            }

            if (isset($userData['password'])) {
                // Vous pouvez choisir de mettre à jour le mot de passe uniquement s'il est fourni dans les données
                $hashedPassword = $passwordHasher->hashPassword($user, $userData['password']);
                $user->setPassword($hashedPassword);
            }

            if (isset($userData['email'])) {
                $user->setEmail($userData['email']);
            }

    // Mise à jour des champs de l'utilisateur
    if (isset($userData['username'])) {
        $user->setUsername($userData['username']);
    }

    if (isset($userData['password'])) {
        // Vous pouvez choisir de mettre à jour le mot de passe uniquement s'il est fourni dans les données
        $hashedPassword = $passwordHasher->hashPassword($user, $userData['password']);
        $user->setPassword($hashedPassword);
    }

    if (isset($userData['email'])) {
        $user->setEmail($userData['email']);
    }

    // Mise à jour de la relation "team"
    if (isset($userData['team'])) {
        // Récupérez l'ID de la nouvelle équipe
        $newTeamId = $userData['team'];

        // Récupérez l'équipe depuis la base de données
        $newTeam = $entityManager->getRepository(Team::class)->find($newTeamId);

        if (!$newTeam) {
            return $this->json(['error' => 'New team not found.'], Response::HTTP_NOT_FOUND);
        }

        // Associez l'utilisateur à la nouvelle équipe
        $user->setTeam($newTeam);
    }

    // Mise à jour de la relation "league"
    if (isset($userData['league'])) {
        // Récupérez l'ID de la nouvelle ligue
        $newLeagueId = $userData['league'];

        // Récupérez la ligue depuis la base de données
        $newLeague = $entityManager->getRepository(League::class)->find($newLeagueId);

        if (!$newLeague) {
            return $this->json(['error' => 'New league not found.'], Response::HTTP_NOT_FOUND);
        }

        // Associez l'utilisateur à la nouvelle ligue
        $user->setLeague($newLeague);
    }            
            // Validez les modifications apportées à l'utilisateur
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }

                return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Persistez les modifications en base de données
            $entityManager->flush();

            // Retournez une réponse JSON avec les données de l'utilisateur mis à jour
            return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user_get_item']]);
        }
}
