<?php

namespace App\Tests\Unit;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $game = new Game();
        $game->setCreatedAt(new \DateTime())
        ->setWinner('Winner');

        $errors = $container->get('validator')->validate($game);

        $this->assertCount(0, $errors);
    }
}
