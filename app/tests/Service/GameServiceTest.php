<?php

namespace App\Tests\Service;

use App\Entity\Round;
use App\Entity\User;
use App\Repository\UserScoreRepository;
use App\Repository\WordRepository;
use App\Service\GameService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    public function testAddWordMaxReached(): void
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $userScoreRepository = $this->createStub(UserScoreRepository::class);
        $wordRepository = $this->createStub(WordRepository::class) ;

        $wordRepository->method('findAllForRoundAndUser')->willReturn(range(0, 9));

        $gameService = new GameService($entityManager, $userScoreRepository, $wordRepository);

        $this->assertSame('max_reached', $gameService->addWord(new Round(), new User(), ''));
    }
}
