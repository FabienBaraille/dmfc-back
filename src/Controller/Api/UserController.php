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
        // données à retourner


        return $this->json(
            $user = $userRepository->findAll(),
            
            200,

            [],
            
            ['groups' => 'get_login']
        );

    }
    /**
     *GET users by id*
    *@Route("/api/user/{id}", name="app_api_user_by_id", methods={"GET"})
    */
    public function getUserById(User $user):JsonResponse
    {
    return $this->json(
        $user,
        200,
        [],
        ['groups' => 'get_login']
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
        $league = new League();
        $league->setLeagueName('Nom de la ligue');
        $league->setCreatedAt(new \DateTime('now'));
        $user->setLeague($league);
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










