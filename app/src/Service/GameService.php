<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Round;
use App\Entity\RoundStats;
use App\Entity\User;
use App\Entity\UserScore;
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
        $result = $this->wordRepository->findAllForCurrentWord($round, $round->getCurrentWord()->getWord());

        return array_map(function (Word $word) {
            return $word->getUser()->getNickname();
        }, $result);
    }

    public function addUser(Game $game, User $user): void
    {
        if (!$game->getUsers()->contains($user)) {
            $game->addUser($user);
            $this->entityManager->persist(new UserScore(0, $user, $game));
            $this->entityManager->flush();
        }
    }

    public function startGame(Game $game)
    {
        $game->setStatus(Game::STATUS_OPEN);
        $game->setCurrentRound($game->getRounds()->first());
        $this->entityManager->flush();
    }

    public function calculateRound(Game $game): void
    {
        foreach ($this->wordRepository->countAllForCurrentRound($game->getCurrentRound()) as $entry) {
            $roundStats = new RoundStats($entry['word'], $entry['amount'], $game->getCurrentRound());
            $this->entityManager->persist($roundStats);
            if ($game->getCurrentRound()->getCurrentWord() === null) {
                $game->getCurrentRound()->setCurrentWord($roundStats);
            }
        }

        $game->setStatus(Game::STATUS_CLOSE);
        $this->entityManager->flush();
    }

    public function calculateCurrentWord(Round $currentRound): void
    {
        $currentRound->getWords()->removeElement($currentRound->getCurrentWord());
        $currentWord = $currentRound->getWords()->first();
        if ($currentWord->getCount() === 1) {
            $words = [];
            foreach ($currentRound->getWords() as $roundStats) {
                $words[] = $roundStats->getWord();
                $this->addPointsToUsers($roundStats, $currentRound->getGame()->getUsers()->count(), $currentRound, false);
            }
            $currentRound->getWords()->clear();
            $megaWord = new RoundStats(implode(', ', $words), 0, $currentRound);
            $this->entityManager->persist($megaWord);
            $currentRound->setCurrentWord($megaWord);
        } else {
            $this->addPointsToUsers($currentWord, $currentRound->getGame()->getUsers()->count(), $currentRound, false);
            $currentRound->setCurrentWord($currentWord);
        }
        $this->entityManager->flush();
    }

    public function nextRound(Game $game): void
    {
        $game->setCurrentRound($game->getRounds()->get($game->getRounds()->indexOf($game->getCurrentRound()) + 1));
        $game->setStatus(Game::STATUS_OPEN);
        $this->entityManager->flush();
    }

    public function endGame(Game $game)
    {
        $game->setStatus(Game::STATUS_END);
        $this->entityManager->flush();
    }

    public function addPointsToUsers(RoundStats $currentWord, int $allUsers, Round $round, bool $flush): void
    {
        $amount = $currentWord->getCount();
        $word = $currentWord->getWord();
        if ($amount < $allUsers) {
            foreach ($this->wordRepository->findAllForCurrentWord($round, $word) as $userWord) {
                $userScore = $this->userScoreRepository->findOneBy(['user' => $userWord->getUser(), 'game' => $round->getGame()]);
                $userScore->setScore($userScore->getScore() + $amount);
            }
        }
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function createNewGame(User $user): Game
    {
        $value = file_get_contents(__DIR__.'/../../data/game_id_generator_list/animals.txt');

        $lines = explode("\n", $value);
        for ($i = 0; $i < 3; ++$i) {
            $animals[] = trim($lines[random_int(0, \count($lines) - 1)]);
        }
        $game = new Game(implode('-', $animals), $user);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }
}
