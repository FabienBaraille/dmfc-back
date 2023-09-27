    <?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Repository\LeagueRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeagueController extends AbstractController
{
    /**
     * GET users of a specific league
     *
     * @Route("/api/league/{leagueId}/users", name="app_api_league_users", methods={"GET"})
     */
    public function getUsersByLeague(LeagueRepository $leagueRepository, $leagueId, UserRepository $userRepository): JsonResponse
    {
        $league = $leagueRepository->find($leagueId);

        if (!$league) {
            return $this->json(['error' => 'League not found'], 404);
        }

        $users = $userRepository->findBy(['league' => $league]);

        $jsonData = $this->json($users, 200, [], ['groups' => 'get_login'])->getContent();

        return new JsonResponse($jsonData, 200, [], true);
    }
}