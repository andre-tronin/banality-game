<?php

namespace App\Controller\Json;

use App\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('/{game_id}/game-status', name: 'game_status'),
    Entity('game', expr: 'repository.find(game_id)')
]
class GameStatusController extends AbstractController
{
    public function __invoke(Game $game): Response
    {
        return $this->json($game->getStatus());
    }
}
