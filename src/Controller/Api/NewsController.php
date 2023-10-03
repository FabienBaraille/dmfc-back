<?php

namespace App\Controller\Api;

use App\Repository\LeagueRepository;
use App\Repository\NewsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class NewsController extends AbstractController
{
    /**
     * GET news collection
     * 
     * @Route("/api/news", name="app_api_news", methods={"GET"})
     */
    public function getNewsAll(NewsRepository $newsRepository): JsonResponse
    {       
        $news = $newsRepository->findAll();

        return $this->json(
            [
                $news,
            ],
            200,
            [],
            ['groups' => 'news_get_collection']);
    }
}