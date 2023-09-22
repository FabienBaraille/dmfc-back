<?php

namespace App\Entity;

use App\Repository\SrpredictionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SrpredictionRepository::class)
 */
class Srprediction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $predictedWinnigTeam;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $predictedPointDifference;

    /**
     * @ORM\Column(type="smallint")
     */
    private $validationStatus;

    /**
     * @ORM\Column(type="smallint")
     */
    private $pointScored;

    /**
     * @ORM\Column(type="smallint")
     */
    private $bonusPointsErned;

    /**
     * @ORM\Column(type="smallint")
     */
    private $bonusBookie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="srpredictions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="srpredictions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPredictedWinnigTeam(): ?string
    {
        return $this->predictedWinnigTeam;
    }

    public function setPredictedWinnigTeam(string $predictedWinnigTeam): self
    {
        $this->predictedWinnigTeam = $predictedWinnigTeam;

        return $this;
    }

    public function getPredictedPointDifference(): ?string
    {
        return $this->predictedPointDifference;
    }

    public function setPredictedPointDifference(string $predictedPointDifference): self
    {
        $this->predictedPointDifference = $predictedPointDifference;

        return $this;
    }

    public function getValidationStatus(): ?int
    {
        return $this->validationStatus;
    }

    public function setValidationStatus(int $validationStatus): self
    {
        $this->validationStatus = $validationStatus;

        return $this;
    }

    public function getPointScored(): ?int
    {
        return $this->pointScored;
    }

    public function setPointScored(int $pointScored): self
    {
        $this->pointScored = $pointScored;

        return $this;
    }

    public function getBonusPointsErned(): ?int
    {
        return $this->bonusPointsErned;
    }

    public function setBonusPointsErned(int $bonusPointsErned): self
    {
        $this->bonusPointsErned = $bonusPointsErned;

        return $this;
    }

    public function getBonusBookie(): ?int
    {
        return $this->bonusBookie;
    }

    public function setBonusBookie(int $bonusBookie): self
    {
        $this->bonusBookie = $bonusBookie;

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

    public function getGame(): ?Game
    {
        return $this->Game;
    }

    public function setGame(?Game $Game): self
    {
        $this->Game = $Game;

        return $this;
    }
}
