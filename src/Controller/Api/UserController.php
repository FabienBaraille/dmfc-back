<?php

namespace App\Controller\Api;

use App\Entity\Team;
use App\Entity\User;
use App\Entity\League;
use App\Entity\Selection;
use App\Repository\UserRepository;
use App\Repository\LeagueRepository;
use App\Repository\SelectionRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
     * Obtenir un utilisateur par nom d'utilisateur
     *
     * @Route("/api/user/{username}", name="app_api_user_by_username", methods={"GET"})
     */
    public function getUserByUsername(UserRepository $userRepository, string $username): JsonResponse
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if ($user) {
            return $this->json(
                [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                    'team' => $user->getTeam(),
                    'league_id' => $user->getLeague(),
                    'title' => $user->getTitle(),
                    'score' => $user->getScore()
                ],
                200,
                [],
                ['groups' => 'user_get_item']
            );
        } else {
            return $this->json(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create User
     *
     * @Route("/api/user/new", name="app_api_user_new_post", methods={"POST"})
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

        $user->setCreatedAt(new \DateTime('now'));

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }            
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        // Retournez une réponse JSON avec les données de l'utilisateur mis à jour
        $responseData = [
        'message' => 'Utilisateur créé avec succès.',
        'user' => $user, // Les données de l'utilisateur mis à jour
        ];

        return $this->json(
        $responseData,
        Response::HTTP_CREATED,
        [],
        ['groups' => ['user_get_item']]
        );
    }
    /**
     * Create DMFC
     *
     * @Route("/api/user/new/dmfc", name="app_api_user_new_dmfc_post", methods={"POST"})
     */
    public function createDmfc(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserRepository $userRepository, LeagueRepository $leagueRepository, TeamRepository $teamRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $jsonContent = $request->getContent();
        $requestData = json_decode($jsonContent, true);

        // Vérifiez si le nom d'utilisateur ou l'email est déjà utilisé
        $existingUser = $userRepository->findOneBy(['username' => $requestData['username']]);
        if ($existingUser) {
            return $this->json(['error' => 'Nom d\'utilisateur déjà utilisé.'], Response::HTTP_CONFLICT);
        }

        $existingEmail = $userRepository->findOneBy(['email' => $requestData['email']]);
        if ($existingEmail) {
            return $this->json(['error' => 'Email déjà utilisé.'], Response::HTTP_CONFLICT);
        }

        $existingLeague = $leagueRepository->findOneBy(['leagueName' => $requestData['leagueName']]);
        if ($existingLeague) {
            return $this->json(['error' => 'League déjà existante.'], Response::HTTP_CONFLICT);
        }

        $league = new League;

        $league->setCreatedAt(new \DateTime('now'));
        $league->setLeagueName($requestData['leagueName']);

        $errorsLeague = $validator->validate($league);

        if (count($errorsLeague) > 0) {
            $errorMessages = [];
            foreach ($errorsLeague as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($league);
        $entityManager->flush();

        $allTeams = $teamRepository->findAll();
        foreach ($allTeams as $team) {
            $selection = new Selection;
            $selection->setTeams($team);
            $selection->setLeagues($league);
            $errorsSelection = $validator->validate($selection);

            if (count($errorsSelection) > 0) {
                $errorMessages = [];
                foreach ($errorsSelection as $error) {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $entityManager->persist($selection);
            $entityManager->flush();
        }

        $user = new User;

        $user->setUsername($requestData['username']);
        $user->setEmail($requestData['email']);
        $user->setRoles($requestData['roles']);
        $user->setPassword($requestData['password']);
        $user->setLeague($league);

        // Hasher le mot de passe avant la sauvegarde
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $user->setCreatedAt(new \DateTime('now'));

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        // Retournez une réponse JSON avec les données de l'utilisateur mis à jour
        $responseData = [
        'message' => 'Utilisateur créé avec succès.',
        'user' => $user, // Les données de l'utilisateur mis à jour
        ];

        return $this->json(
        $responseData,
        Response::HTTP_CREATED,
        [],
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
              return $this->json(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
          }

          $entityManager->remove($user);
          $entityManager->flush();

          return $this->json(['message' => 'Utilisateur supprimé avec succès.'], Response::HTTP_OK);
      }

     /**
     * Update User by Id
     *
     * @Route("/api/user/{id}", name="app_api_user_update", methods={"PUT"})
     */
    public function updateUser(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $id, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Récupérez l'utilisateur existant depuis la base de données
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['message' => "Cet utilisateur n'existe pas."], Response::HTTP_NOT_FOUND);
        }

        // Désérialisez les données JSON de la requête en un objet User
        $jsonContent = $request->getContent();
        $userData = json_decode($jsonContent, true);

        // Mise à jour des champs de l'utilisateur
        if (isset($userData['username'])) {
            $existingUserWithUsername = $entityManager->getRepository(User::class)->findOneBy(['username' => $userData['username']]);
            if (($existingUserWithUsername && $existingUserWithUsername !== $user)) {
                return $this->json(['error' => 'Le nom d\'utilisateur est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
            }
            $user->setUsername($userData['username']);
        }

        if (isset($userData['password'])) {
            // Mettre à jour le mot de passe uniquement s'il est fourni dans les données
            $hashedPassword = $passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
        }

        if (isset($userData['email'])) {
            $existingUserWithEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
            if ($existingUserWithEmail && $existingUserWithEmail !== $user) {
                return $this->json(['error' => 'L\'adresse email est déjà utilisée.'], Response::HTTP_BAD_REQUEST);
            }
            $user->setEmail($userData['email']);
        }

        // Mise à jour de la relation "league"
        if (isset($userData['league'])) {
            // Récupérez l'ID de la nouvelle ligue
            $newLeagueId = $userData['league'];

            // Récupérez la ligue depuis la base de données
            $newLeague = $entityManager->getRepository(League::class)->find($newLeagueId);

            if (!$newLeague) {
                return $this->json(['error' => "Cette équipe n'existe pas."], Response::HTTP_NOT_FOUND);
            }

            // Associez l'utilisateur à la nouvelle ligue
            $user->setLeague($newLeague);
        }

        // Mise à jour de la relation "team"
        if (isset($userData['team'])) {
            // Récupérez l'ID de la nouvelle équipe
            $newTeamId = $userData['team'];

            // Récupérez l'équipe depuis la base de données
            $newTeam = $entityManager->getRepository(Team::class)->find($newTeamId);

            if (!$newTeam) {
                return $this->json(['error' => "Cette équipe n'existe pas."], Response::HTTP_NOT_FOUND);
            }

            // Associez l'utilisateur à la nouvelle équipe
            $user->setTeam($newTeam);
        }

    

        // Validez les modifications apportées à l'utilisateur
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];
        
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $message = $error->getMessage();
        
                if (!isset($errorMessages[$propertyPath])) {
                    $errorMessages[$propertyPath] = [];
                }
        
                $errorMessages[$propertyPath][] = $message;
            }
        
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setUpdatedAt(new \Datetime);

        // Persistez les modifications en base de données
        $entityManager->flush();

        // Retournez une réponse JSON avec les données de l'utilisateur mis à jour
        $responseData = [
            'message' => 'Utilisateur modifié avec succès.',
            'user' => $user, // Les données de l'utilisateur mis à jour
        ];
        
        return $this->json(
            $responseData,
            Response::HTTP_OK,
            [],
            ['groups' => ['user_get_item']]
        );
    }


    /**
     * @Route("/api/user/{id}/dmfc", name="app_api_update_user_by_dmfc", methods={"PUT"})
     */
    public function updateUserByDmfc(Request $request, User $user, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        // Récupérez les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Mise à jour des propriétés de l'utilisateur avec les nouvelles données
        if (!empty($data['title'])) {
            $user->setTitle($data['title']);
        } else {
            $user->setTitle(null);
        }

        if (isset($data['role'])) {
            $user->setRoles($data['role']);
        }

        if (isset($data['score'])) {
            $user->setScore($data['score']);
        }

        if (isset($data['scoreTOP'])) {
            $user->setScoreTOP($data['scoreTOP']);
        }
        if (isset($data['scorePO'])) {
            $user->setScorePO($data['scorePO']);
        }
        
        if (isset($data['oldPosition'])) {
            $user->setOldPosition($data['oldPosition']);
        }

        // Mise à jour de la relation "league" (ajout d'une vérification isset)
        if (isset($data['league'])) {
            if ($data['league'] === 0) {
                $user->setLeague(null);
            } else {
                $newLeagueId = $data['league'];
                $newLeague = $entityManager->getRepository(League::class)->find($newLeagueId);

                if (!$newLeague) {
                    return $this->json(['error' => "Cette ligue n'existe pas."], Response::HTTP_NOT_FOUND);
                }

                $user->setLeague($newLeague);
            }
        }

        // Validez les données mises à jour avec le groupe de validation
        $violations = $validator->validate($user, null, null);

        if (count($violations) > 0) {
            // Gérez les erreurs de validation, par exemple, renvoyez une réponse JSON d'erreur
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], 400);
        }

        // Sauvegardez les modifications dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Créez une réponse JSON pour indiquer que la mise à jour a réussi
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            ['groups' => ['user_get_item']]
        );
    }
}
