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
    public function getUserAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        
        return $this->json(
            $users,
            200,
            [],
            ['groups' => 'get_login']
        );
    } 

    /**
     * GET users collection
     *
     * @Route("/api/user/{id}", name="app_api_user_by_id", methods={"GET"})
     */
    public function getUserById(UserRepository $userRepository, $userId): JsonResponse
    {
        $user = $userRepository->findBy(['id' => $userId]);

    
        return $this->json(
        
            $user,
            200,
            [],
            ['groups' => 'get_id']
    
        );
    }

}
