<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class mainController extends AbstractController
{
    /**
     * Homepage back
     * @Route("/back", name="app_back_home")
     */
    public function home(): Response
    {
        return $this->render('back/main/home.html.twig', [
            
        ]);
    }
}
