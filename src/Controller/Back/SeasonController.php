<?php

namespace App\Controller\Back;

use DateTime;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SeasonController extends AbstractController
{
    /**
     * @Route("/back/season", name="app_back_season", methods={"GET"})
     */
    public function index(SeasonRepository $seasonRepository): Response
    {
        return $this->render('back/season/index.html.twig', [
            'seasons' => $seasonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/back/season/{id}", name="app_back_season_show", methods={"GET"})
     */
    public function show(Season $season): Response
    {
        return $this->render('back/season/show.html.twig', [
        'season' => $season,
        ]);
    }
    
    /**
     * @Route("/back/new/season", name="app_back_season_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SeasonRepository $seasonRepository): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);
        $season->setCreatedAt(new DateTime());
        if ($form->isSubmitted() && $form->isValid()) {

            $seasonRepository->add($season, true);

            return $this->redirectToRoute('app_back_season', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/season/new.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back/season/{id}/edit", name="app_back_season_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Season $season, SeasonRepository $seasonRepository): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);
        $season->setUpdatedAt(new DateTime());


        if ($form->isSubmitted() && $form->isValid()) {
                
            $seasonRepository->add($season, true);

            return $this->redirectToRoute('app_back_season', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back/season/{id}/delete", name="app_back_season_delete", methods={"POST"})
     */
    public function delete(Request $request, Season $season, SeasonRepository $seasonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $seasonRepository->remove($season, true);
        }

        return $this->redirectToRoute('app_back_season', [], Response::HTTP_SEE_OTHER);
    }
}
