<?php

namespace App\Controller\Api;

use App\Entity\League;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;



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
     * Create user
     * 
     * @Route("/api/user/new", name="app_api_user_new_post", methods={"POST"})
     */
    public function postCollection(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
    // la donnée JSON est reçue depuis le contenu de requête
    $jsonContent = $request->getContent();

    // on désérialise le JSON en Objet de type App\Entity\User
    // 1. le JSON d'entrée
    // 2. le type d'objet à créer (User)
    // 3. le format d'entre
    $user = $serializer->deserialize($jsonContent, User::class, 'json');

    // validation de l'entité (via les contraintes déjà présentes dans l'entité)
    $errors = $validator->validate($user);

    // erreurs de validation ?
    if (count($errors) > 0) {


        // tableau d'erreurs à retourner
        $errorMessages = [];
        // on récupère chque erreur de la liste des erreurs
        // dd($errors);

        foreach ($errors as $error) {
            // on crée un tableau de tableau dont la clé est la propriété en erreur
            // les messages s'ajoutent dans le sous-tableau
            // => même structure que celle des Flash Messages
            $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);

        // à défaut on peut retourner ceci
        // return $this->json($errors);
    }

    $entityManager->persist($user);
    $entityManager->flush();

}
}








