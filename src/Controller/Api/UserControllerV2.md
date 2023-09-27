<?php

namespace App\Controller\Api;

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


class UserController extends AbstractController
{
    /**
     * CREATE a new user
     *
     * @Route("/api/user", name="app_api_create_user", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $requestData = $request->getContent();

        // Désérialisez les données JSON en un objet User.
        $user = $serializer->deserialize($requestData, User::class, 'json');

        // Validez l'objet User.
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        // Enregistrez le nouvel utilisateur dans la base de données.
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Sérialisez l'objet User créé en utilisant les groupes de sérialisation définis dans l'entité User.
        $jsonData = $serializer->serialize($user, 'json', ['groups' => 'get_login']);

        return new JsonResponse($jsonData, 201);
    }

    /**
     * UPDATE user by ID
     *
     * @Route("/api/user/{id}", name="app_api_update_user", methods={"PUT"})
     */
    public function updateUser(Request $request, UserRepository $userRepository, $id, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $user = $userRepository->find($id);

        if (!$user) {
            // L'utilisateur avec l'ID donné n'a pas été trouvé
            return $this->json(['error' => 'User not found'], 404);
        }

        $requestData = $request->getContent();

        // Désérialisez les données JSON dans l'objet User existant.
        $updatedUser = $serializer->deserialize($requestData, User::class, 'json');

        // Validez l'objet User mis à jour.
        $errors = $validator->validate($updatedUser);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        // Mettez à jour les propriétés de l'objet User existant avec les données mises à jour.
        $user->setUsername($updatedUser->getUsername());
        $user->setEmail($updatedUser->getEmail());

        // Enregistrez les modifications dans la base de données.
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        // Sérialisez l'objet User mis à jour en utilisant les groupes de sérialisation définis dans l'entité User.
        $jsonData = $serializer->serialize($user, 'json', ['groups' => 'get_login']);

        return new JsonResponse($jsonData, 200);
    }

    /**
     * DELETE user by ID
     *
     * @Route("/api/user/{id}", name="app_api_delete_user", methods={"DELETE"})
     */
    public function deleteUser(UserRepository $userRepository, $id)
    {
        $user = $userRepository->find($id);

        if (!$user) {
            // L'utilisateur avec l'ID donné n'a pas été trouvé
            return $this->json(['error' => 'User not found'], 404);
        }

        // Supprimez l'utilisateur de la base de données.
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, 204); // 204 No Content
    }
}
