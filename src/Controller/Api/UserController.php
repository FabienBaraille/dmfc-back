<?php

namespace App\Controller\Api;



use App\Entity\User;
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
     * @Route("/api/user", name="app_api_users_post", methods={"POST"})
     */
    public function postUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        $user = $serializer->deserialize($jsonContent, User::class,'json');

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

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_user', ['id' => $user->getId()]),
            ],
            ['groups' => ['get_login']]
        );
    }   
}

