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
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAndTimeOfMatch;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $visitorScore;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $homeScore;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $winner;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $visitorOdd;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $homeOdd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Srprediction::class, mappedBy="Game", orphanRemoval=true)
     */
    private $srpredictions;

    /**
     * @ORM\ManyToOne(targetEntity=Round::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;

    /**
     * @ORM\OneToMany(targetEntity=Team::class, mappedBy="game")
     */
    private $teams;

    public function __construct()
    {
        $this->srpredictions = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAndTimeOfMatch(): ?\DateTimeInterface
    {
        return $this->dateAndTimeOfMatch;
    }

    public function setDateAndTimeOfMatch(\DateTimeInterface $dateAndTimeOfMatch): self
    {
        $this->dateAndTimeOfMatch = $dateAndTimeOfMatch;

        return $this;
    }

    public function getVisitorScore(): ?int
    {
        return $this->visitorScore;
    }

    public function setVisitorScore(?int $visitorScore): self
    {
        $this->visitorScore = $visitorScore;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(?int $homeScore): self
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(?string $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getVisitorOdd(): ?float
    {
        return $this->visitorOdd;
    }

    public function setVisitorOdd(?float $visitorOdd): self
    {
        $this->visitorOdd = $visitorOdd;

        return $this;
    }

    public function getHomeOdd(): ?float
    {
        return $this->homeOdd;
    }

    public function setHomeOdd(?float $homeOdd): self
    {
        $this->homeOdd = $homeOdd;

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

    /**
     * @return Collection<int, Srprediction>
     */
    public function getSrpredictions(): Collection
    {
        return $this->srpredictions;
    }

    public function addSrprediction(Srprediction $srprediction): self
    {
        if (!$this->srpredictions->contains($srprediction)) {
            $this->srpredictions[] = $srprediction;
            $srprediction->setGame($this);
        }

        return $this;
    }

    public function removeSrprediction(Srprediction $srprediction): self
    {
        if ($this->srpredictions->removeElement($srprediction)) {
            // set the owning side to null (unless already changed)
            if ($srprediction->getGame() === $this) {
                $srprediction->setGame(null);
            }
        }

        return $this;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): self
    {
        $this->round = $round;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setGame($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getGame() === $this) {
                $team->setGame(null);
            }
        }

        return $this;
    }
}
