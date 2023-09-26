<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Game;
use App\Entity\News;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Round;
use App\Entity\League;
use App\Entity\Season;
use App\Entity\Leaderboard;
use App\Entity\Srprediction;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    // on récupère la connexion DBAL pour exécuter des requêtes SQL (pour le TRUNCATE)
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Permet de TRUNCATE les tables et de remettre les ID à 1
     */
    private function truncate()
    {
        // On passe en mode SQL !
        // Désactivation la vérification des contraintes FK
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // On tronque
        $this->connection->executeQuery('TRUNCATE TABLE game');
        $this->connection->executeQuery('TRUNCATE TABLE leaderboard');
        $this->connection->executeQuery('TRUNCATE TABLE league');
        $this->connection->executeQuery('TRUNCATE TABLE news');
        $this->connection->executeQuery('TRUNCATE TABLE round');
        $this->connection->executeQuery('TRUNCATE TABLE season');
        $this->connection->executeQuery('TRUNCATE TABLE srprediction');
        $this->connection->executeQuery('TRUNCATE TABLE team');
        $this->connection->executeQuery('TRUNCATE TABLE user');
    }
    
    public function load(ObjectManager $manager): void
    {
        // on TRUNCATE en amont des fixtures
        $this->truncate();
        
        // Définissez la date de création
        $createdAt = new DateTime();

        $faker = Factory::create('fr_FR');    
            
        // League
            $league = new League;
            $league->setLeagueName("Ligue des justiciers");
            $league->setLeagueDescription("Bienvenue dans la ligue des justiciers !");

            $league->setCreatedAt($createdAt);
            $manager->persist($league);

        // League
            $league2 = new League;
            $league2->setLeagueName("Ligue des Pompons");
            $league2->setLeagueDescription("Bienvenue dans la ligue des pompons !");

            $league2->setCreatedAt($createdAt);
            $manager->persist($league2);
        

        // User
            // Admin
            $userA = new User();
            $userA->setUsername('admin');
            $userA->setEmail('admin@admin.com');
            $userA->setRole(['ROLE_ADMIN']);
            $userA->setPassword('admin');
            $userA->setTitle("C'est moi, Fabien! Le super Admin");
            $userA->setLeague($league);
            $userA->setCreatedAt($createdAt);

            $manager->persist($userA);

            // Maître du jeu
            $userDMFC = new User;
            $userDMFC->setUsername('DMFC');
            $userDMFC->setEmail('dmfc@dmfc.com');
            $userDMFC->setRole(['ROLE_DMFC']);
            $userDMFC->setPassword('dmfc');
            $userDMFC->setTitle("C'est moi, le maître du jeu");
            $userDMFC->setSeasonPlayed("3");
            $userDMFC->setLeague($league);
            $userDMFC->setCreatedAt($createdAt);

            $manager->persist($userDMFC);

            // Maître du jeu
            $userDMFC2 = new User;
            $userDMFC2->setUsername('DMFC2');
            $userDMFC2->setEmail('dmfc2@dmfc2.com');
            $userDMFC2->setRole(['ROLE_DMFC']);
            $userDMFC2->setPassword('dmfc2');
            $userDMFC2->setTitle("C'est moi, le maître du jeu N°2");
            $userDMFC2->setSeasonPlayed("3");
            $userDMFC2->setLeague($league2);
            $userDMFC2->setCreatedAt($createdAt);

            $manager->persist($userDMFC2);

            // Joueurs
            $users = [];
            for ($i = 1; $i <= 5; $i++) {
                $user = new User();
                $user->setUsername("joueur$i");
                $user->setEmail("joueur$i@example.com");
                $user->setRole(['ROLE_JOUEUR']);
                $user->setPassword("joueur$i");
                $user->setTitle("C'est moi, le joueur $i");
                $user->setScore($faker->numberBetween(10, 50));
                $user->setOldPosition($faker->numberBetween(1, 5));
                $user->setPosition($faker->numberBetween(1, 5));
                $user->setSeasonPlayed(3);
                $user->setLeague($league);
                $user->setCreatedAt($createdAt);

                $users[] = $user;
                $manager->persist($user);
            }

            for ($i = 6; $i <= 10; $i++) {
                $user2 = new User();
                $user2->setUsername("joueur$i");
                $user2->setEmail("joueur$i@example.com");
                $user2->setRole(['ROLE_JOUEUR']);
                $user2->setPassword("joueur$i");
                $user2->setTitle("C'est moi, le joueur $i");
                $user2->setScore($faker->numberBetween(10, 50));
                $user2->setOldPosition($faker->numberBetween(1, 5));
                $user2->setPosition($faker->numberBetween(1, 5));
                $user2->setSeasonPlayed(3);
                $user2->setLeague($league2);
                $user2->setCreatedAt($createdAt);

                $users[] = $user2;
                $manager->persist($user2);
            }

        // News
            $news = new News;
            $news->setLeague($league);
            $news->setTitle("Début de saison pour les jusiciers");
            $news->setDescription("C'est un nouveau jour pour nous !");
            $news->setCreatedAt($createdAt);
                
            $manager->persist($news);

            $news2 = new News;
            $news2->setLeague($league2);
            $news2->setTitle("Début de saison pour les Pompons");
            $news2->setDescription("C'est un nouveau jour pour nous !");
            $news2->setCreatedAt($createdAt);
                
            $manager->persist($news2);
        

        // Season
            $season = new Season;
            $season->setYear("2023-2024");
            $season->setCreatedAt($createdAt);
            
            $manager->persist($season);

        // Leaderboard
            foreach ($users as $user) {
                $leaderboard = new Leaderboard();
                $leaderboard->setFinalScore($user->getScore());
                $leaderboard->setFinalRank($user->getPosition());
                $leaderboard->setCreatedAt(new DateTime());
                $leaderboard->setSeason($season);
                $leaderboard->setUser($user);

                $leaderboards[] = $leaderboard;

                // Triez les classements par score final
                usort($leaderboards, function ($a, $b) {
                    return $b->getFinalScore() - $a->getFinalScore();
                });

                // Affectez le classement final en fonction de l'ordre trié
                $rank = 1;
                foreach ($leaderboards as $leaderboard) {
                    $leaderboard->setFinalRank($rank);
                    $manager->persist($leaderboard);
                    $rank++;
                }
            }                    



        // Round
            $rounds = [];
            for ($i = 1; $i <= 5; $i++) {
                $round = new Round();
                $round->setName("round$i");
                $round->setCategory("Saison Régulière");
                $round->setCreatedAt($createdAt);
                $round->setLeague($league);
                $round->setUser($userDMFC);
                $round->setSeason($season);

                $rounds[] = $round;
                $manager->persist($round);
            }  
        
            $rounds2 = [];
            for ($i = 1; $i <= 5; $i++) {
                $round2 = new Round();
                $round2->setName("round$i");
                $round2->setCategory("Saison Régulière");
                $round2->setCreatedAt($createdAt);
                $round2->setLeague($league2);
                $round2->setUser($userDMFC2);
                $round2->setSeason($season);

                $rounds2[] = $round2;
                $manager->persist($round2);
            }     

        // Game
            $games = [];
                for ($i = 1; $i <= 60; $i++) {
                    $game = new Game();
                    $game->setDateAndTimeOfMatch($faker->dateTimeBetween('now', '+1 years'));

                    // Choisissez un tour aléatoire parmi les tours créés
                    $randomRoundIndex = array_rand($rounds);
                    $randomRound = $rounds[$randomRoundIndex];

                    $game->setRound($randomRound);
                    $game->setCreatedAt($createdAt);
                    $games[] = $game;
                }
            
        // Team
            $teamsData = [
                ['name' => 'Atlanta Hawks', 'conference' => 'Eastern', 'trigram' => 'ATL'],
                ['name' => 'Boston Celtics', 'conference' => 'Eastern', 'trigram' => 'BOS'],
                ['name' => 'Brooklyn Nets', 'conference' => 'Eastern', 'trigram' => 'BKN'],
                ['name' => 'Charlotte Hornets', 'conference' => 'Eastern', 'trigram' => 'CHA'],
                ['name' => 'Chicago Bulls', 'conference' => 'Eastern', 'trigram' => 'CHI'],
                ['name' => 'Cleveland Cavaliers', 'conference' => 'Eastern', 'trigram' => 'CLE'],
                ['name' => 'Dallas Mavericks', 'conference' => 'Western', 'trigram' => 'DAL'],
                ['name' => 'Denver Nuggets', 'conference' => 'Western', 'trigram' => 'DEN'],
                ['name' => 'Detroit Pistons', 'conference' => 'Eastern', 'trigram' => 'DET'],
                ['name' => 'Golden State Warriors', 'conference' => 'Western', 'trigram' => 'GSW'],
                ['name' => 'Houston Rockets', 'conference' => 'Western', 'trigram' => 'HOU'],
                ['name' => 'Indiana Pacers', 'conference' => 'Eastern', 'trigram' => 'IND'],
                ['name' => 'LA Clippers', 'conference' => 'Western', 'trigram' => 'LAC'],
                ['name' => 'Los Angeles Lakers', 'conference' => 'Western', 'trigram' => 'LAL'],
                ['name' => 'Memphis Grizzlies', 'conference' => 'Western', 'trigram' => 'MEM'],
                ['name' => 'Miami Heat', 'conference' => 'Eastern', 'trigram' => 'MIA'],
                ['name' => 'Milwaukee Bucks', 'conference' => 'Eastern', 'trigram' => 'MIL'],
                ['name' => 'Minnesota Timberwolves', 'conference' => 'Western', 'trigram' => 'MIN'],
                ['name' => 'New Orleans Pelicans', 'conference' => 'Western', 'trigram' => 'NOP'],
                ['name' => 'New York Knicks', 'conference' => 'Eastern', 'trigram' => 'NYK'],
                ['name' => 'Oklahoma City Thunder', 'conference' => 'Western', 'trigram' => 'OKC'],
                ['name' => 'Orlando Magic', 'conference' => 'Eastern', 'trigram' => 'ORL'],
                ['name' => 'Philadelphia 76ers', 'conference' => 'Eastern', 'trigram' => 'PHI'],
                ['name' => 'Phoenix Suns', 'conference' => 'Western', 'trigram' => 'PHX'],
                ['name' => 'Portland Trail Blazers', 'conference' => 'Western', 'trigram' => 'POR'],
                ['name' => 'Sacramento Kings', 'conference' => 'Western', 'trigram' => 'SAC'],
                ['name' => 'San Antonio Spurs', 'conference' => 'Western', 'trigram' => 'SAS'],
                ['name' => 'Toronto Raptors', 'conference' => 'Eastern', 'trigram' => 'TOR'],
                ['name' => 'Utah Jazz', 'conference' => 'Western', 'trigram' => 'UTA'],
                ['name' => 'Washington Wizards', 'conference' => 'Eastern', 'trigram' => 'WAS'],
            ];

            
            foreach ($teamsData as $teamData) {
                $team = new Team();
                $team->setName($teamData['name']);
                $team->setConference($teamData['conference']);
                $team->setTrigram($teamData['trigram']);
                $team->setCreatedAt($createdAt);
                $team->setNbSelectedAway($faker->numberBetween(0, 6));
                $team->setNbSelectedHome($faker->numberBetween(0, 6));

                $manager->persist($team);
            }
                $manager->persist($game);

        // SR Prediction
            foreach ($users as $user) {
                foreach ($games as $game) {

                    $srPrediction = new Srprediction();

                    $srPrediction->setGame($game);

                    $srPrediction->setUser($user);
                    $srPrediction->setPredictedWinnigTeam($teamsData[$faker->numberBetween(0, count($teamsData) - 1)]['name']);
                    $srPrediction->setPredictedPointDifference($faker->numberBetween(5, 30));
                    $srPrediction->setValidationStatus($faker->randomElement(['Saved', 'Validated', 'Published']));
                    $srPrediction->setPointScored(0);
                    $srPrediction->setBonusPointsErned(0);
                    $srPrediction->setBonusBookie(0);
                    $srPrediction->setCreatedAt($createdAt);

                    $manager->persist($srPrediction);
                }
            }

        $manager->flush();
    }
}
