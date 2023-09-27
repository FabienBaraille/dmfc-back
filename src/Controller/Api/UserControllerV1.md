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
     * GET users collection
     *
     * @Route("/api/user", name="app_api_user", methods={"GET"})
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        // Vous pouvez ajuster ici les données renvoyées selon vos besoins.
        // Par exemple, si vous souhaitez inclure uniquement certaines propriétés de chaque utilisateur.
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                // Ajoutez d'autres propriétés ici selon vos besoins.
            ];
        }

        return $this->json($data, 200, [], ['groups' => 'get_login']);
    }

    /**
     * GET users collection
     *
     * @Route("/api/user/{id}", name="app_api_user_by_id", methods={"GET"})
     */
    public function getUserById(UserRepository $userRepository, $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            // L'utilisateur avec l'ID donné n'a pas été trouvé
            return $this->json(['error' => 'User not found'], 404);
        }

        // Vous pouvez ajuster ici les données renvoyées selon vos besoins.
        // Par exemple, si vous souhaitez inclure uniquement certaines propriétés de l'utilisateur.
        $data = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            // Ajoutez d'autres propriétés ici selon vos besoins.
        ];

        return $this->json($data, 200, [], ['groups' => 'get_login']);
    }
}