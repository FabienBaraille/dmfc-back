<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SelectionController extends AbstractController
{
    /**
     * @Route("/api/selection", name="app_api_selection")
     */
    public function index(): Response
    {
        return $this->render('api/selection/index.html.twig', [
            'controller_name' => 'SelectionController',
        ]);
    }
}
