<?php

namespace App\Controller;

use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, GameService $gameService): Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('create')) {
            $game = $gameService->createNewGame($this->getUser());

            return $this->redirectToRoute('admin', ['game_id' => $game->getId()]);
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
