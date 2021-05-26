<?php

namespace App\Controller\Json;

use App\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('/{game_id}/game-status', name: 'game_status', requirements: ['game_id' => '^[a-z]+-[a-z]+-[a-z]+$']),
    Route('/{game_id}/admin/game-status', name: 'game_status_admin', requirements: ['game_id' => '^[a-z]+-[a-z]+-[a-z]+$']),
    Entity('game', expr: 'repository.find(game_id)')
]
class GameStatusController extends AbstractController
{
    public function __invoke(Game $game): Response
    {
        if ($game->getStatus() === Game::STATUS_CLOSE) {
            return $this->json($game->getCurrentRound()->getCurrentWord()->getWord());
        }

        return $this->json($game->getStatus().\count($game->getUsers()));
    }
}
