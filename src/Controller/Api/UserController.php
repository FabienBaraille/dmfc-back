<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/api/user", name="app_api_user_login", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->json(['result' => $users], 200,[], ['groups' => 'get_login' ]);
    }




    /**
     * @Route("/api/user/{id}", name="app_api_user_login", methods={"GET"})
     */
    public function getUserById(UserRepository $userRepository): JsonResponse
    {

        return $this->json($userRepository->findBy());
    }





    
    /**
     * Create User
     * 
     * @Route("/api/user", name="app_api_user_login", methods={"POST"})
     */
    public function postLogin(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        $user = $serializer->deserialize($jsonContent, User::class, 'json');
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

    }
}
