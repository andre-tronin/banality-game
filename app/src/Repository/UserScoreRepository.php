<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\UserScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserScore[]    findAll()
 * @method UserScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserScore::class);
    }

    /**
     * @return UserScore[] Returns an array of UserScore objects
     */
    public function findByGame(Game $game): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.game = :game')
            ->setParameter('game', $game)
            ->orderBy('u.score', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?UserScore
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
