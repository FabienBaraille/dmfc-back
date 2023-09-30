<?php

namespace App\Controller\Api;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class NewsController extends AbstractController
{
    /**
     * GET news collection
     * 
     * @Route("/api/news", name="app_api_news", methods={"GET"})
     */
    public function getNewsAll(NewsRepository $newsRepository): JsonResponse
    {
        return $this->json([
            $newsRepository->findAll(),
            200,
            [],
            ['groups' => 'news_get_collection']
        ]);
    }
}
