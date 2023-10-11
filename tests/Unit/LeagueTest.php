<?php

namespace App\Tests\Unit;

use App\Entity\League;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeagueTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $league = new League();
        $league->setCreatedAt(new \DateTime())
        ->setLeagueName('Username')
        ->setLeagueDescription('Description');

        $errors = $container->get('validator')->validate($league);

        $this->assertCount(0, $errors);
    }
}
