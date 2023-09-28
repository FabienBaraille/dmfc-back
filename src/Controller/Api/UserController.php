<?php

namespace App\Controller\Api;

use App\Entity\League;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use id;

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
   * @Route("/api/user", name="app_api_user_post", methods={"POST"})
   */
  public function postUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
  {
      $jsonContent = $request->getContent();
      $userData = json_decode($jsonContent, true);

      if (!isset($userData['league'])) {
          return $this->json(['error' => 'Le champ "league" est requis.'], Response::HTTP_BAD_REQUEST);
      }

      $leagueId = $userData['league'];

      $league = $entityManager->getRepository(League::class)->find($leagueId);

      $user = $serializer->deserialize($jsonContent, User::class, 'json');
      $user->setLeague($league);

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
    * @Route("/api/user/{id}", name="app_api_user_update", methods={"PUT"})
    */
    public function updateUser(Request $request, User $user, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $jsonContent = $request->getContent();

        $updatedUser = $serializer->deserialize($jsonContent, User::class, 'json');

        $errors = $validator->validate($updatedUser);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newUsername = $updatedUser->getUsername();
        if ($newUsername !== null) {
            $user->setUserName($newUsername);
        }

        $newPassword = $updatedUser->getPassword();
        if ($newPassword !== null) {
            $user->setPassword($newPassword);
        }

        $newEmail = $updatedUser->getEmail();
        if ($newEmail !== null) {
            $user->setEmail($newEmail);
        }

        $newTeam = $updatedUser->getTeam();
        if ($newTeam !== null) {
            $user->setTeam($newTeam);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            ['groups' => ['get_login']]
        );
    }
}
