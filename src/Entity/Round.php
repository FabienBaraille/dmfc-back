<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use App\Entity\Season;
use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Groups({"rounds_get_collection", "games_get_collection", "games_get_post"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Groups({"leagues_get_collection","rounds_get_collection", "games_get_collection", "games_get_post"})
    */
    private $name;

    /**
     * @ORM\Column(type="string", length=60)
     * @Groups({"rounds_get_collection"})
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="rounds", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rounds_get_collection"})
     */
    private $season;

    /**
     * @ORM\ManyToOne(targetEntity=League::class, inversedBy="rounds")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"games_get_post"})
     */
    private $league;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rounds")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rounds_get_collection"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="round", orphanRemoval=true)
     * @Groups({"rounds_get_collection"})
     */
    private $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setRound($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getRound() === $this) {
                $game->setRound(null);
            }
        }

        return $this;
    }
}
