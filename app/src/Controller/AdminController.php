<?php

namespace App\Controller;

use App\Entity\Game;
use App\Security\GameVoter;
use App\Service\GameService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    #[
        Route('/{game_id}/admin', name: 'admin', requirements: ['game_id' => '^[a-z]+-[a-z]+-[a-z]+$']),
        Entity('game', expr: 'repository.find(game_id)'),
        IsGranted(GameVoter::ADMIN, subject: 'game')
    ]
    public function index(Game $game, Request $request): Response
    {
        $userStats = $this->gameService->getUserStats($game);
        $currentRoundNumber = $game->getRounds()->indexOf($game->getCurrentRound()) + 1;
        switch ($game->getStatus()) {
            case Game::STATUS_START:
                return $this->startAction($game, $userStats, $currentRoundNumber, $request);
            case Game::STATUS_OPEN:
                return $this->openAction($game, $userStats, $currentRoundNumber, $request);
            case Game::STATUS_CLOSE:
                return $this->closeAction($game, $userStats, $currentRoundNumber, $request);
            case Game::STATUS_END:
                return $this->endAction($userStats);
            default:
                return $this->createAction($game, $userStats, $request);
        }
    }

    public function startAction(Game $game, array $userStats, int $currentRoundNumber, Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('start')) {
            $this->gameService->startGame($game);

            return $this->redirectToRoute('admin', ['game_id' => $game->getId()]);
        }

        return $this->render('admin/start.html.twig', [
            'userStats' => $userStats,
            'currentRoundNumber' => $currentRoundNumber,
            'game' => $game,
        ]);
    }

    public function openAction(Game $game, array $userStats, int $currentRoundNumber, Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('calculateRound')) {
            $this->gameService->calculateRound($game);
            $currentWord = $game->getCurrentRound()->getCurrentWord();
            $this->gameService->addPointsToUsers($currentWord, $game->getUsers()->count(), $game->getCurrentRound(), true);

            return $this->redirectToRoute('admin', ['game_id' => $game->getId()]);
        }

        return $this->render('admin/open.html.twig', [
            'userStats' => $userStats,
            'topic' => $game->getCurrentRound()->getTopic(),
            'currentRoundNumber' => $currentRoundNumber,
        ]);
    }

    public function closeAction(Game $game, array $userStats, int $currentRoundNumber, Request $request): Response
    {
        $currentRound = $game->getCurrentRound();
        if ($request->isMethod(Request::METHOD_POST)) {
            if ($request->request->has('calculateCurrentWord')) {
                $this->gameService->calculateCurrentWord($currentRound);
            } elseif ($request->request->has('nextRound')) {
                $this->gameService->nextRound($game);

                return $this->redirectToRoute('admin', ['game_id' => $game->getId()]);
            } elseif ($request->request->has('end')) {
                $this->gameService->endGame($game);

                return $this->redirectToRoute('admin', ['game_id' => $game->getId()]);
            }
        }
        if ($currentRound->getCurrentWord()->getCount() !== 0) {
            $buttonAction = 'calculateCurrentWord';
            $buttonName = 'next word';
        } elseif ($game->getRounds()->last() === $currentRound) {
            $buttonAction = 'end';
            $buttonName = 'end game';
        } else {
            $buttonAction = 'nextRound';
            $buttonName = 'next round';
        }

        return $this->render('admin/close.html.twig', [
            'userStats' => $userStats,
            'words' => $this->gameService->getUserWords($currentRound, $this->getUser()),
            'topic' => $currentRound->getTopic(),
            'currentWord' => $currentRound->getCurrentWord()->getWord(),
            'usersWithCurrentWord' => implode(', ', $this->gameService->getUsersWithWord($currentRound)),
            'buttonAction' => $buttonAction,
            'buttonName' => $buttonName,
            'currentRoundNumber' => $currentRoundNumber,
        ]);
    }

    public function endAction(array $userStats): Response
    {
        return $this->render('admin/end.html.twig', [
            'userStats' => $userStats,
        ]);
    }

    public function createAction(Game $game, array $userStats, Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('rounds')) {
            $this->gameService->addRounds($game, $request->request->get('rounds'));

            return $this->redirectToRoute('admin', ['game_id' => $game->getId()]);
        }

        return $this->render('admin/create.html.twig', [
            'userStats' => $userStats,
        ]);
    }
}
