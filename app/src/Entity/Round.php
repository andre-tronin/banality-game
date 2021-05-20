<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundRepository::class)
 */
class Round
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $topic;

    /**
     * @ORM\OneToMany(targetEntity=RoundStats::class, mappedBy="round", orphanRemoval=true)
     */
    private Collection $words;

    /**
     * @ORM\OneToOne(targetEntity=RoundStats::class, cascade={"persist", "remove"})
     */
    private ?RoundStats $currentWord;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="rounds")
     * @ORM\JoinColumn(nullable=false)
     */
    private Game $game;

    public function __construct(string $topic, Game $game)
    {
        $this->words = new ArrayCollection();
        $this->topic = $topic;
        $this->game = $game;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @return Collection|RoundStats[]
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function addWord(RoundStats $word): self
    {
        if (!$this->words->contains($word)) {
            $this->words[] = $word;
            $word->setRound($this);
        }

        return $this;
    }

    public function removeWord(RoundStats $word): self
    {
        if ($this->words->removeElement($word)) {
            // set the owning side to null (unless already changed)
            if ($word->getRound() === $this) {
                $word->setRound(null);
            }
        }

        return $this;
    }

    public function getCurrentWord(): ?RoundStats
    {
        return $this->currentWord;
    }

    public function setCurrentWord(?RoundStats $currentWord): self
    {
        $this->currentWord = $currentWord;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
