<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SeasonRepository;
use App\Entity\Round;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=SeasonRepository::class)
 * @UniqueEntity(fields="year", message="Ce nom de saison existe déjà.")
 */
class Season
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"rounds_get_collection","Seasons_get_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"seasons_get_collection","leaderbord"})
     * @Assert\NotBlank
     */
    private $year;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Leaderboard::class, mappedBy="Season", orphanRemoval=true)
     */
    private $leaderboards;

    /**
     * @ORM\OneToMany(targetEntity=Round::class, mappedBy="season", orphanRemoval=true)
     * @Groups({"Seasons_get_collection"})
     */
    private $rounds;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startSeason;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startPlayoff;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Comment;

    public function __construct()
    {
        $this->leaderboards = new ArrayCollection();
        $this->rounds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

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
     * @return Collection<int, Leaderboard>
     */
    public function getLeaderboards(): Collection
    {
        return $this->leaderboards;
    }

    public function addLeaderboard(Leaderboard $leaderboard): self
    {
        if (!$this->leaderboards->contains($leaderboard)) {
            $this->leaderboards[] = $leaderboard;
            $leaderboard->setSeason($this);
        }

        return $this;
    }

    public function removeLeaderboard(Leaderboard $leaderboard): self
    {
        if ($this->leaderboards->removeElement($leaderboard)) {
            // set the owning side to null (unless already changed)
            if ($leaderboard->getSeason() === $this) {
                $leaderboard->setSeason(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Round>
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setSeason($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getSeason() === $this) {
                $round->setSeason(null);
            }
        }

        return $this;
    }

    public function getStartSeason(): ?\DateTimeInterface
    {
        return $this->startSeason;
    }

    public function setStartSeason(?\DateTimeInterface $startSeason): self
    {
        $this->startSeason = $startSeason;

        return $this;
    }

    public function getStartPlayoff(): ?\DateTimeInterface
    {
        return $this->startPlayoff;
    }

    public function setStartPlayoff(?\DateTimeInterface $startPlayoff): self
    {
        $this->startPlayoff = $startPlayoff;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->Comment;
    }

    public function setComment(?string $Comment): self
    {
        $this->Comment = $Comment;

        return $this;
    }
}
