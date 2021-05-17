<?php

namespace App\Entity;

use App\Repository\RoundStatsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundStatsRepository::class)
 */
class RoundStats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $word;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private int $count;

    /**
     * @ORM\ManyToOne(targetEntity=Round::class, inversedBy="words")
     * @ORM\JoinColumn(nullable=false)
     */
    private Round $round;

    public function __construct(string $word, int $count, Round $round)
    {
        $this->word = $word;
        $this->count = $count;
        $this->round = $round;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getRound(): Round
    {
        return $this->round;
    }

    public function setRound(Round $round): self
    {
        $this->round = $round;

        return $this;
    }
}
