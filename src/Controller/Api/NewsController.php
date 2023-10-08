<?php

namespace App\Controller\Api;

use App\Entity\News;
use App\Entity\League;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    /**
     * Create News
     * 
     * @Route("/api/news/new", name="app_api_news_new_post", methods={"POST"})
     */
    public function createNews(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();
        $newsData = json_decode($jsonContent, true);

        $news = $serializer->deserialize($jsonContent, News::class,'json');

        // Vérifiez si le champ "league" existe dans les données JSON
        if (!isset($newsData['league'])) {
            return $this->json(['error' => 'La ligue liée est requise.'], Response::HTTP_BAD_REQUEST);
        }        

        // Vérifiez si le champ "league" existe et si oui, associez la news à une ligue
        if (isset($newsData['league'])) {
            $leagueId = $newsData['league'];
            $league = $entityManager->getRepository(League::class)->find($leagueId);
            if (!$league) {
                return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
            }
            $news->setLeague($league);
        }

        $news->setCreatedAt(new \DateTime('now'));

        $errors = $validator->validate($news);
        
        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }            
                return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($news);
        $entityManager->flush();

        // Retournez une réponse JSON avec les données de l'utilisateur mis à jour
        $responseData = [
            'message' => 'Utilisateur créer avec succès.',
            'news' => $news, // Les données de l'utilisateur mis à jour
        ];

            return $this->json(
                $responseData,
                Response::HTTP_CREATED,
                [
                    'Location' => $this->generateUrl('app_api_news', ['id' => $news->getId()]),
                ],
                ['groups' => ['news_get_item', 'news_get_collection']]
            );
    }

        /**
         * Update News
         * 
         * @Route("/api/news/{id}", name="app_api_news_update", methods={"PUT"})
         */
        public function updateNews(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, News $news): Response
        {
            $jsonContent = $request->getContent();
            $newsData = json_decode($jsonContent, true);

            // Vérifiez si le champ "league" existe dans les données JSON
            if (!isset($newsData['league'])) {
                return $this->json(['error' => 'La ligue liée est requise.'], Response::HTTP_BAD_REQUEST);
            }

            // Vérifiez si le champ "league" existe et si oui, associez la news à une ligue
            if (isset($newsData['league'])) {
                $leagueId = $newsData['league'];
                $league = $entityManager->getRepository(League::class)->find($leagueId);
                if (!$league) {
                    return $this->json(['error' => 'Ligue non trouvée.'], Response::HTTP_NOT_FOUND);
                }
                $news->setLeague($league);
            }

            // Mettez à jour le champ "title" si présent dans les données JSON
            if (isset($newsData['title'])) {
                $news->setTitle($newsData['title']);
            }

            // Mettez à jour le champ "description" si présent dans les données JSON
            if (isset($newsData['description'])) {
                $news->setDescription($newsData['description']);
            }

            $news->setUpdatedAt(new \DateTime('now'));

            $errors = $validator->validate($news);

            if (count($errors) > 0) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }

                return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $entityManager->flush();

            return $this->json(
                $news,
                Response::HTTP_OK,
                [],
                ['groups' => ['news_get_item', 'news_get_collection']]
            );
        }    

        /**
         * Delete News
         * 
         * @Route("/api/news/{id}", name="app_api_news_delete", methods={"DELETE"})
         */
        public function deleteNews(EntityManagerInterface $entityManager, $id): Response
        {
            $news = $entityManager->getRepository(News::class)->find($id);

            if (!$news) {
                return $this->json(['error' => 'News non trouvée.'], Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($news);
            $entityManager->flush();

            return $this->json(['message' => 'News supprimée avec succès.'], Response::HTTP_OK);
        }
}