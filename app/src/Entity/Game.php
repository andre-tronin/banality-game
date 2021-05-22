<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    public const STATUS_START = 'start';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSE = 'close';
    public const STATUS_END = 'end';

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('start', 'open', 'close', 'end')", options={"default":null}, nullable=true)
     */
    private ?string $status;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('ru', 'de', 'en')", options={"default":"en"}, nullable=false)
     */
    private string $locale;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private bool $useDictionary;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     *
     * @var Collection<int, User>
     */
    private Collection $users;

    /**
     * @ORM\OneToMany(targetEntity=Round::class, mappedBy="game", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @var Collection<int, Round>
     */
    private Collection $rounds;

    /**
     * @ORM\OneToOne(targetEntity=Round::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Round $currentRound;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $admin;

    public function __construct(string $id, User $admin, string $locale = 'en', bool $useDictionary = true)
    {
        $this->users = new ArrayCollection();
        $this->rounds = new ArrayCollection();
        $this->id = $id;
        $this->admin = $admin;
        $this->locale = $locale;
        $this->useDictionary = $useDictionary;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, User>|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Round>|Round[]
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setGame($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getGame() === $this) {
                $round->setGame(null);
            }
        }

        return $this;
    }

    public function getCurrentRound(): ?Round
    {
        return $this->currentRound;
    }

    public function setCurrentRound(Round $currentRound): self
    {
        $this->currentRound = $currentRound;

        return $this;
    }

    public function getAdmin(): User
    {
        return $this->admin;
    }

    public function setAdmin(User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isUseDictionary(): bool
    {
        return $this->useDictionary;
    }

    public function setUseDictionary(bool $useDictionary): self
    {
        $this->useDictionary = $useDictionary;

        return $this;
    }
}
