<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManager;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class NewsController extends AbstractController
{
    /**
     * GET news collection
     * 
     * @Route("/api/news", name="app_api_news", methods={"GET"})
     */
    public function getNewsAll(Request $request, EntityManagerInterface $entityManager, NewsRepository $newsRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        $jsonContent = $request->getContent();
        $userData = json_decode($jsonContent, true);
        $leagueId = $userData['league'];

        $league = $entityManager->getRepository(League::class)->find($leagueId);
    
        if (!$league) {
            return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
        }
        
        
        return $this->json([
            $newsRepository->findAll(),
            200,
            [],
            ['groups' => 'news_get_collection']
        ]);
    }
}
