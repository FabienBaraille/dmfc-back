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
     * @Route("/api/user/league", name="app_api_league_get_user_by_league", methods={"GET"})
     */
    public function getUsersByLeague(League $league = null,UserRepository $userRepository): JsonResponse
    {
    
        if ($league === null) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }
        return $this->json($userRepository->findByLeague($league), 200, [], ['groups' => 'get_login_league' ]);
    }

    /**
     * GET users collection
     * 
     * @Route("/api/user", name="app_api_user", methods={"GET"})
     */
    public function getUserAll(UserRepository $userRepository): JsonResponse
    {
        // données à retourner
        ;

        return $this->json(
            $user = $userRepository->findAll(),
            
            200,

            [],
            
            ['groups' => 'get_login']
        );
    }
}
