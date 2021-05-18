<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Round;
use App\Entity\RoundStats;
use App\Entity\UserScore;
use App\Entity\Word;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('user');
        $game = new Game('fish-duck-rock', $this->getReference('admin'));
        $game->setStatus(Game::STATUS_OPEN);
        $game->addUser($user);
        $manager->persist($game);

        $round1 = new Round();
        $round1->setTopic('topic one');
        $round1->setGame($game);
        $manager->persist($round1);

        $game->setCurrentRound($round1);

        $round2 = new Round();
        $round2->setTopic('topic two');
        $round2->setGame($game);
        $manager->persist($round2);

        $roundStat = new RoundStats('sample', 1, $round1);
        $manager->persist($roundStat);

        $userScore = new UserScore(20, $user, $game);
        $manager->persist($userScore);

        $round1->setCurrentWord($roundStat);

        $manager->persist(new Word('sample', $user, $round1));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
