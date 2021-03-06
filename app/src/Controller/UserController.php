<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\UserScore;
use App\Security\GameVoter;
use App\Service\GameService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    #[
        Route('/{game_id}', name: 'user', requirements: ['game_id' => '^[a-z]+-[a-z]+-[a-z]+$']),
        Entity('game', expr: 'repository.find(game_id)'),
        IsGranted(GameVoter::PLAY, subject: 'game')
    ]
    public function index(Game $game, Request $request): Response
    {
        $userStats = $this->gameService->getUserStats($game);
        switch ($game->getStatus()) {
            case Game::STATUS_START:
                return $this->startAction($game, $userStats);
            case Game::STATUS_OPEN:
                return $this->openAction($game, $userStats, $request);
            case Game::STATUS_CLOSE:
                return $this->closeAction($game, $userStats);
            case Game::STATUS_END:
                return $this->endAction($userStats);
            default:
                return $this->endAction($userStats);
        }
    }

    /**
     * @param UserScore[] $userStats
     */
    public function startAction(Game $game, array $userStats): Response
    {
        if (!$game->getUsers()->contains($this->getUser())) {
            $this->gameService->addUser($game, $this->getUser());
            $userStats = $this->gameService->getUserStats($game);
        }

        return $this->render('user/start.html.twig', [
            'userStats' => $userStats,
        ]);
    }

    /**
     * @param UserScore[] $userStats
     */
    public function openAction(Game $game, array $userStats, Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $result = $this->gameService->addWord($game->getCurrentRound(), $this->getUser(), $request->request->get('word', ''));
        }

        return $this->render('user/open.html.twig', [
            'userStats' => $userStats,
            'words' => $this->gameService->getUserWords($game->getCurrentRound(), $this->getUser()),
            'topic' => $game->getCurrentRound()->getTopic(),
            'result' => $result ?? null,
        ]);
    }

    /**
     * @param UserScore[] $userStats
     */
    public function closeAction(Game $game, array $userStats): Response
    {
        return $this->render('user/close.html.twig', [
            'userStats' => $userStats,
            'words' => $this->gameService->getUserWords($game->getCurrentRound(), $this->getUser()),
            'topic' => $game->getCurrentRound()->getTopic(),
            'currentWord' => $game->getCurrentRound()->getCurrentWord()->getWord(),
            'usersWithCurrentWord' => implode(', ', $this->gameService->getUsersWithWord($game->getCurrentRound())),
        ]);
    }

    /**
     * @param UserScore[] $userStats
     */
    public function endAction(array $userStats): Response
    {
        return $this->render('user/end.html.twig', [
            'userStats' => $userStats,
        ]);
    }
}
