<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Round;
use App\Entity\User;
use App\Entity\Word;
use App\Repository\UserScoreRepository;
use App\Repository\WordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;

class GameService
{
    private UserScoreRepository $userScoreRepository;
    private WordRepository $wordRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserScoreRepository $userScoreRepository,
        WordRepository $wordRepository
    ) {
        $this->userScoreRepository = $userScoreRepository;
        $this->wordRepository = $wordRepository;
        $this->entityManager = $entityManager;
    }

    public function getUserStats(Game $game): array
    {
        return $this->userScoreRepository->findByGame($game);
    }

    public function getUserWords(Round $round, User $user): array
    {
        return $this->wordRepository->findAllForRoundAndUser($user, $round);
    }

    public function addWord(Round $round, User $user, string $word): ?string
    {
        $word = mb_strtolower(trim($word));
        $words = $this->getUserWords($round, $user);
        if (\count($words) > 9) {
            return 'max_reached';
        }
        if (!empty(array_filter(
            $words,
            function (Word $item) use ($word) {
                return $item->getWord() === $word;
            }
        ))) {
            return 'already_submitted'.htmlspecialchars($word, \ENT_QUOTES, 'UTF-8');
        }

        $finder = new Finder();
        $wordEntity = null;
        foreach ($finder->files()->in(__DIR__.'/../../data/dictionary/') as $file) {
            if ($file->getFilename() === 'russian_nouns.txt') {
                $dict = $file->getContents();
                if (strstr($dict, $word."\n") === false) {
                    return 'not_found'.htmlspecialchars($word, \ENT_QUOTES, 'UTF-8');
                } else {
                    $wordEntity = new Word($word, $user, $round);
                    break;
                }
            }
        }
        if ($wordEntity === null) {
            throw new \Exception('Dictionary not found');
        }

        $this->entityManager->persist($wordEntity);
        $this->entityManager->flush();

        return null;
    }

    public function getUsersWithWord(Round $round): array
    {
        $result = $this->wordRepository->findAllForCurrentWord($round);

        return array_map(function (Word $word) {
            return $word->getUser()->getNickname();
        }, $result);
    }

    public function addUser(Game $game, User $user): void
    {
        if (!$game->getUsers()->contains($user)) {
            $game->addUser($user);
            $this->entityManager->flush();
        }
    }
}
