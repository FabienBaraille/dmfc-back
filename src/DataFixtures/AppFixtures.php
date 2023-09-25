<?php

namespace App\DataFixtures;

use App\Entity\Leaderboard;
use DateTime;
use App\Entity\User;
use App\Entity\League;
use App\Entity\News;
use App\Entity\Round;
use App\Entity\Season;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Définissez la date de création
        $createdAt = new DateTime();

        // League
            $league = new League;
            $league->setLeagueName("Ligue des justiciers");
            $league->setLeagueDescription("");

            $league->setCreatedAt($createdAt);
            $manager->persist($league);
            

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

            // Joueurs
            $userJA = new User;
            $userJA->setUsername('joueurA');
            $userJA->setEmail('joueurA@joueurA.com');
            $userJA->setRole(['ROLE_JOUEUR']);
            $userJA->setPassword('joueurA');
            $userJA->setTitle("C'est moi, le joueur A");
            $userJA->setScore(28);
            $userJA->setOldPosition(2);
            $userJA->setPosition(1);
            $userJA->setSeasonPlayed(3);
            $userJA->setLeague($league);
            $userJA->setCreatedAt($createdAt);

            $manager->persist($userJA);

            $userJB = new User;
            $userJB->setUsername('joueurB');
            $userJB->setEmail('joueurB@joueurB.com');
            $userJB->setRole(['ROLE_JOUEUR']);
            $userJB->setPassword('joueurB');
            $userJB->setTitle("C'est moi, le joueurB");
            $userJB->setScore(24);
            $userJB->setOldPosition(1);
            $userJB->setPosition(2);
            $userJB->setSeasonPlayed(3);
            $userJB->setLeague($league);
            $userJB->setCreatedAt($createdAt);

            $manager->persist($userJB);

            $userJC = new User;
            $userJC->setUsername('joueurC');
            $userJC->setEmail('joueurC@joueurC.com');
            $userJC->setRole(['ROLE_JOUEUR']);
            $userJC->setPassword('joueurC');
            $userJC->setTitle("C'est moi, le joueur C");
            $userJC->setScore(16);
            $userJC->setOldPosition(3);
            $userJC->setPosition(3);
            $userJC->setSeasonPlayed(3);
            $userJC->setLeague($league);
            $userJC->setCreatedAt($createdAt);

            $manager->persist($userJC);

        // News
            $news = new News;
            $news->setLeague($league);
            $news->setTitle("Début de saison");
            $news->setDescription("C'est un nouveau jour pour nous !");
            $news->setCreatedAt($createdAt);
                
            $manager->persist($news);


        // Season
            $season = new Season;
            $season->setYear("2023-2024");
            $season->setCreatedAt($createdAt);
            
            $manager->persist($season);

        // Leaderboard
            // Joueur A
            $leaderboardA = new Leaderboard;
            $leaderboardA->setFinalScore(54);
            $leaderboardA->setFinalRank(1);
            $leaderboardA->setCreatedAt($createdAt);
            $leaderboardA->setSeason($season);
            $leaderboardA->setUser($userJA);

            $manager->persist($leaderboardA);
                    
            // Joueur B
            $leaderboardB = new Leaderboard;
            $leaderboardB->setFinalScore(48);
            $leaderboardB->setFinalRank(2);
            $leaderboardB->setCreatedAt($createdAt);
            $leaderboardB->setSeason($season);
            $leaderboardB->setUser($userJB);

            $manager->persist($leaderboardB);

            // Joueur C
            $leaderboardC = new Leaderboard;
            $leaderboardC->setFinalScore(16);
            $leaderboardC->setFinalRank(3);
            $leaderboardC->setCreatedAt($createdAt);
            $leaderboardC->setSeason($season);
            $leaderboardC->setUser($userJB);

            $manager->persist($leaderboardC);

        // Round
            $round = new Round;
            $round->setName("On se chauffe");
            $round->setCategory("Saison Régulière");
            $round->setCreatedAt($createdAt);
            $round->setLeague($league);
            $round->setUser($userDMFC);
            $round->setSeason($season);

            $manager->persist($round);
            
            $manager->flush();
    }
}
