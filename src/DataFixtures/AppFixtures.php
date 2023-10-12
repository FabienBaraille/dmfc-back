<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;


class AppFixtures extends Fixture
{
    // on récupère la connexion DBAL pour exécuter des requêtes SQL (pour le TRUNCATE)
    private $connection;
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory ,Connection $connection)
    {
        $this->connection = $connection;
        $this->encoderFactory = $encoderFactory;
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
                 
        // Création de l'administrateur
        $user = new User();
        $user->setUsername('SuperAdmin');
        $user->setEmail('superadmin@superadmin.com');
        $user->setRoles(['ROLE_ADMIN']);
        
        // Récupérer l'encodeur pour l'entité User
        $encoder = $this->encoderFactory->getEncoder($user);

        // Hasher le mot de passe
        $hashedPassword = $encoder->encodePassword('superadmin', $user->getSalt());
        $user->setPassword($hashedPassword);
        
        $user->setCreatedAt($createdAt);

        $manager->persist($user);

        // Team
            $teamsData = [
                ['name' => 'Atlanta Hawks', 'conference' => 'Eastern', 'trigram' => 'ATL', 'logo' => 'logos/ATL_1986.png'],
                ['name' => 'Boston Celtics', 'conference' => 'Eastern', 'trigram' => 'BOS', 'logo' => 'logos/BOS_1947.png'],
                ['name' => 'Brooklyn Nets', 'conference' => 'Eastern', 'trigram' => 'BKN', 'logo' => 'logos/BKN_2013.png'],
                ['name' => 'Charlotte Hornets', 'conference' => 'Eastern', 'trigram' => 'CHA', 'logo' => 'logos/CHA_1988.png'],
                ['name' => 'Chicago Bulls', 'conference' => 'Eastern', 'trigram' => 'CHI', 'logo' => 'logos/CHI_1967.png'],
                ['name' => 'Cleveland Cavaliers', 'conference' => 'Eastern', 'trigram' => 'CLE', 'logo' => 'logos/CLE_1970.png'],
                ['name' => 'Dallas Mavericks', 'conference' => 'Western', 'trigram' => 'DAL', 'logo' => 'logos/DAL_1980.png'],
                ['name' => 'Denver Nuggets', 'conference' => 'Western', 'trigram' => 'DEN', 'logo' => 'logos/DEN_1974.png'],
                ['name' => 'Detroit Pistons', 'conference' => 'Eastern', 'trigram' => 'DET', 'logo' => 'logos/DET_1958.png'],
                ['name' => 'Golden State Warriors', 'conference' => 'Western', 'trigram' => 'GSW', 'logo' => 'logos/GSW_1971.png'],
                ['name' => 'Houston Rockets', 'conference' => 'Western', 'trigram' => 'HOU', 'logo' => 'logos/HOU_1972.png'],
                ['name' => 'Indiana Pacers', 'conference' => 'Eastern', 'trigram' => 'IND', 'logo' => 'logos/IND_1967.png'],
                ['name' => 'LA Clippers', 'conference' => 'Western', 'trigram' => 'LAC', 'logo' => 'logos/LAC_1984.png'],
                ['name' => 'Los Angeles Lakers', 'conference' => 'Western', 'trigram' => 'LAL', 'logo' => 'logos/LAL_1960.png'],
                ['name' => 'Memphis Grizzlies', 'conference' => 'Western', 'trigram' => 'MEM', 'logo' => 'logos/MEM_2001.png'],
                ['name' => 'Miami Heat', 'conference' => 'Eastern', 'trigram' => 'MIA', 'logo' => 'logos/MIA_1988.png'],
                ['name' => 'Milwaukee Bucks', 'conference' => 'Eastern', 'trigram' => 'MIL', 'logo' => 'logos/MIL_1969.png'],
                ['name' => 'Minnesota Timberwolves', 'conference' => 'Western', 'trigram' => 'MIN', 'logo' => 'logos/MIN_1989.png'],
                ['name' => 'New Orleans Pelicans', 'conference' => 'Western', 'trigram' => 'NOP', 'logo' => 'logos/NOP_2013.png'],
                ['name' => 'New York Knicks', 'conference' => 'Eastern', 'trigram' => 'NYK', 'logo' => 'logos/NYK_1946.png'],
                ['name' => 'Oklahoma City Thunder', 'conference' => 'Western', 'trigram' => 'OKC', 'logo' => 'logos/OKC_2008.png'],
                ['name' => 'Orlando Magic', 'conference' => 'Eastern', 'trigram' => 'ORL', 'logo' => 'logos/ORL_1989.png'],
                ['name' => 'Philadelphia 76ers', 'conference' => 'Eastern', 'trigram' => 'PHI', 'logo' => 'logos/PHI_1963.png'],
                ['name' => 'Phoenix Suns', 'conference' => 'Western', 'trigram' => 'PHX', 'logo' => 'logos/PHX_1968.png'],
                ['name' => 'Portland Trail Blazers', 'conference' => 'Western', 'trigram' => 'POR', 'logo' => 'logos/POR_1970.png'],
                ['name' => 'Sacramento Kings', 'conference' => 'Western', 'trigram' => 'SAC', 'logo' => 'logos/SAC_1985.png'],
                ['name' => 'San Antonio Spurs', 'conference' => 'Western', 'trigram' => 'SAS', 'logo' => 'logos/SAS_1976.png'],
                ['name' => 'Toronto Raptors', 'conference' => 'Eastern', 'trigram' => 'TOR', 'logo' => 'logos/TOR_1995.png'],
                ['name' => 'Utah Jazz', 'conference' => 'Western', 'trigram' => 'UTA', 'logo' => 'logos/UTA_1979.png'],
                ['name' => 'Washington Wizards', 'conference' => 'Eastern', 'trigram' => 'WAS', 'logo' => 'logos/WAS_1998.png'],
            ];

            foreach ($teamsData as $teamData) {
                $team = new Team();
                $team->setName($teamData['name']);
                $team->setConference($teamData['conference']);
                $team->setTrigram($teamData['trigram']);
                $team->setLogo($teamData['logo']);
                $team->setCreatedAt(new DateTime());
                
                $manager->persist($team);
            }
                                
        $manager->flush();
    }
}
