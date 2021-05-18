<?php

namespace App\Security;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GameVoter extends Voter
{
    public const PLAY = 'play';
    public const ADMIN = 'admin';

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::PLAY, self::ADMIN])) {
            return false;
        }

        // only vote on `Game` objects
        if (!$subject instanceof Game) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Game object, thanks to `supports()`
        /** @var Game $game */
        $game = $subject;

        switch ($attribute) {
            case self::PLAY:
                return $this->canPlay($game, $user) || $game->getStatus() === Game::STATUS_START;
            case self::ADMIN:
                return $this->canAdmin($game, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canPlay(Game $game, User $user): bool
    {
        return $game->getUsers()->contains($user);
    }

    private function canAdmin(Game $game, User $user): bool
    {
        // this assumes that the Game object has a `getAdmin()` method
        return $user === $game->getAdmin();
    }
}
