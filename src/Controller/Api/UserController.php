<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController
{
    /**
     * GET users collection
     *
     * @Route("/api/user", name="app_api_user", methods={"GET"})
     */
    public function getUserAll(UserRepository $userRepository): JsonResponse
    {
        return $this->json(
            $user = $userRepository->findAll(),
            200,
            [],
            ['groups' => 'get_login']);

    }

    /**
    *GET users collection
    *
    *@Route("/api/user/{id}", name="app_api_user_by_id", methods={"GET"})
    */
    public function getUserById(UserRepository $userRepository, $id): JsonResponse
    {
        return $this->json(
            $user = $userRepository->find($id),
            200,
            [],
            ['groups' => 'get_login']
        );
    }
}