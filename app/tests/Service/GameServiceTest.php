<?php

namespace App\Tests\Service;

use App\Entity\Game;
use App\Entity\Round;
use App\Entity\User;
use App\Repository\UserScoreRepository;
use App\Repository\WordRepository;
use App\Service\GameService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameServiceTest extends TestCase
{
    public function testAddWordMaxReached(): void
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $userScoreRepository = $this->createStub(UserScoreRepository::class);
        $wordRepository = $this->createStub(WordRepository::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $wordRepository->method('findAllForRoundAndUser')->willReturn(range(0, 9));
        $translator->expects($this->once())->method('trans')->with('add_word.max_reached')->willReturn('add_word.max_reached');

        $gameService = new GameService($entityManager, $userScoreRepository, $wordRepository, $translator);

        $this->assertSame('add_word.max_reached', $gameService->addWord(new Round('topic', new Game('fox-duck-fish', new User())), new User(), ''));
    }
}
