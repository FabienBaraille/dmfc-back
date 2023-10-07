<?php

namespace App\Controller\Back;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     * @Route("/back/team", name="app_back_team", methods={"GET"})
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('back/team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    /**
     * @Route("/back/team/{id}", name="app_back_team_show", methods={"GET"})
     */
    public function show(Team $team): Response
    {
        return $this->render('back/team/show.html.twig', [
        'team' => $team,
        ]);
    }

    /**
     * @Route("/back/new/team", name="app_back_team_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TeamRepository $teamRepository): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        $team->setCreatedAt(new DateTime());
        if ($form->isSubmitted() && $form->isValid()) {

            $teamRepository->add($team, true);

            return $this->redirectToRoute('app_back_team', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back/team/{id}/edit", name="app_back_team_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                
            $teamRepository->add($team, true);

            return $this->redirectToRoute('app_back_team', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back/team/{id}/delete", name="app_back_team_delete", methods={"POST"})
     */
    public function delete(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $teamRepository->remove($team, true);
        }

        return $this->redirectToRoute('app_back_team', [], Response::HTTP_SEE_OTHER);
    }
}
