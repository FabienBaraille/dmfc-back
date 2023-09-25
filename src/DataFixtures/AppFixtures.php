<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Définissez la date de création
        $createdAt = new DateTime();

        // User
            // Admin
            $user = new User;
            $user->setUsername('admin');
            $user->setEmail('admin@admin.com');
            $user->setRole(['ROLE_ADMIN']);
            $user->setPassword('admin');
            $user->setTitle("C'est moi, Fabien! Le super Admin");

            $user->setCreatedAt($createdAt);
            $manager->persist($user);

            // Maître du jeu
            $user = new User;
            $user->setUsername('DMFC');
            $user->setEmail('dmfc@dmfc.com');
            $user->setRole(['ROLE_DMFC']);
            $user->setPassword('dmfc');
            $user->setTitle("C'est moi, le maître du jeu");
            $user->setSeasonPlayed("3");

            $user->setCreatedAt($createdAt);
            $manager->persist($user);

            // Joueurs
            $user = new User;
            $user->setUsername('joueur-A');
            $user->setEmail('joueur-A@joueur-A.com');
            $user->setRole(['ROLE_JOUEUR']);
            $user->setPassword('joueur-A');
            $user->setTitle("C'est moi, le joueur A");
            $user->setScore(28);
            $user->setOldPosition(2);
            $user->setPosition(1);
            $user->setSeasonPlayed(3);

            $user->setCreatedAt($createdAt);
            $manager->persist($user);

            $user = new User;
            $user->setUsername('joueur-B');
            $user->setEmail('joueur-B@joueur-B.com');
            $user->setRole(['ROLE_JOUEUR']);
            $user->setPassword('joueur-B');
            $user->setTitle("C'est moi, le joueur B");
            $user->setScore(24);
            $user->setOldPosition(1);
            $user->setPosition(2);
            $user->setSeasonPlayed(3);

            $user->setCreatedAt($createdAt);
            $manager->persist($user);            


            
            $manager->flush();
    }
}
