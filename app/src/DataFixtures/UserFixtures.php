<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNickname('dummy user');

        $admin = new User();
        $admin->setNickname('dummy admin');

        $manager->persist($user);
        $manager->persist($admin);

        $this->addReference('admin', $admin);
        $this->addReference('user', $user);

        $manager->flush();
    }
}
