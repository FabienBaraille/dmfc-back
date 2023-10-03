<?php

namespace App\Controller\Api;

use id;
use DateTime;
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
use Symfony\Component\Security\Core\Security;



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
 * @Route("/api/users/{username}", name="app_api_user_by_username", methods={"GET"})
 */
public function getUserByUsername(UserRepository $userRepository, string $username): JsonResponse
{
    $user = $userRepository->findOneBy(['username' => $username]);

    if ($user) {
        return $this->json(
            [
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                'team' => $user->getTeam(),
                'league_id' => $user->getLeague(),
                'title' => $user->getTitle(),
                'score' => $user->getScore()
                // Ajoutez d'autres propriétés de l'utilisateur si nécessaire
            ],
            200,
            [],
            ['groups' => 'user_get_item']
        );
    } else {
        return $this->json(['message' => 'Utilisateur non trouvé'], 404);
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

    if (!isset($userData['league'])) {
        return $this->json(['error' => 'Le champ "ligue" est requis.'], Response::HTTP_BAD_REQUEST);
    }

    $leagueId = $userData['league'];

    $league = $entityManager->getRepository(League::class)->find($leagueId);

    if (!$league) {
        return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
    }

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
    $user->setLeague($league);

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
            ['groups' => ['get_login']]
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
    * @Route("/api/users/{id}", name="app_api_user_update", methods={"PUT"})
    */
    public function updateUser(UserRepository $userRepository, Request $request, User $user, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();
        $userData = json_decode($jsonContent, true);
    
        // Vérifiez si le nom d'utilisateur ou l'email est déjà utilisé
        $existingUser = $userRepository->findOneBy(['username' => $userData['username']]);
        if ($existingUser && $existingUser !== $user) {
            return $this->json(['error' => 'Nom d\'utilisateur déjà utilisé.'], Response::HTTP_CONFLICT);
        }
    
        // Désérialisez les données JSON en un objet User
        $updatedUser = $serializer->deserialize($jsonContent, User::class, 'json');
    
        // Assurez-vous que la clé 'league' existe dans les données JSON
        if (isset($userData['league']) && is_array($userData['league'])) {
            // Récupérez l'ID de la nouvelle ligue depuis les données JSON
            $newLeagueId = $userData['league']['id'];
    
            // Récupérez l'entité League correspondante depuis la base de données
            $newLeague = $entityManager->getRepository(League::class)->find($newLeagueId);
    
            // Assurez-vous que la ligue existe
            if (!$newLeague) {
                return $this->json(['error' => 'Ligue introuvable.'], Response::HTTP_NOT_FOUND);
            }
    
            // Mettez à jour la propriété 'league' de l'utilisateur avec la nouvelle ligue
            $user->setLeague($newLeague);
        }
    
        // Validez les données de l'utilisateur
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
    
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            ['groups' => ['get_login']]
        );
    }
}
