<?php

namespace App\Entity;

use App\Repository\LeaderboardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LeaderboardRepository::class)
 */
class Leaderboard
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"leagues_get_users"})
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"leagues_get_users", "leaderbord"})
     */
    private $finalScore;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"leagues_get_users", "leaderbord"})
     */
    private $finalRank;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="leaderboards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="leaderboards")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"leaderbord"})
     */
    private $Season;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFinalScore(): ?int
    {
        return $this->finalScore;
    }

    public function setFinalScore(?int $finalScore): self
    {
        $this->finalScore = $finalScore;

        return $this;
    }

    public function getFinalRank(): ?int
    {
        return $this->finalRank;
    }

    public function setFinalRank(int $finalRank): self
    {
        $this->finalRank = $finalRank;

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

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->Season;
    }

    public function setSeason(?Season $Season): self
    {
        $this->Season = $Season;

        return $this;
    }

    
}
