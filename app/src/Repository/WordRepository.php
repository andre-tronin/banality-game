<?php

namespace App\Repository;

use App\Entity\Round;
use App\Entity\User;
use App\Entity\Word;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Word|null find($id, $lockMode = null, $lockVersion = null)
 * @method Word|null findOneBy(array $criteria, array $orderBy = null)
 * @method Word[]    findAll()
 * @method Word[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
    }

    /**
     * @return Word[] Returns an array of Word objects
     */
    public function findAllForCurrentWord(Round $round, string $word): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.round = :round')
            ->andWhere('w.word = :word')
            ->setParameter('round', $round)
            ->setParameter('word', $word)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return [] sorted array of [amount,word]
     */
    public function countAllForCurrentRound(Round $round): array
    {
        return $this->createQueryBuilder('w')
        ->select('COUNT(w.id) as amount', 'w.word')
            ->andWhere('w.round = :round')
            ->setParameter('round', $round)
            ->groupBy('w.word')
            ->orderBy('amount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Word[] Returns an array of Word objects
     */
    public function findAllForRoundAndUser(User $user, Round $round): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.round = :round')
            ->setParameter('user', $user)
            ->setParameter('round', $round)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Word
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
