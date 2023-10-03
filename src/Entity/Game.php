<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"games_get_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"games_get_collection"})
     */
    private $dateAndTimeOfMatch;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"games_get_collection"})
     */
    private $visitorScore;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"games_get_collection"})
     */
    private $homeScore;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"games_get_collection"})
     */
    private $winner;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"games_get_collection"})
     */
    private $visitorOdd;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"games_get_collection"})
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
     * @Groups({"games_get_collection"})
     */
    private $round;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class, inversedBy="games")
     * @Groups({"games_get_collection"})
     */
    private $team;

    public function __construct()
    {
        $this->team = new ArrayCollection();
        $this->srpredictions = new ArrayCollection();
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
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->team->contains($team)) {
            $this->team[] = $team;
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->team->removeElement($team);

        return $this;
    }

    
    
}
