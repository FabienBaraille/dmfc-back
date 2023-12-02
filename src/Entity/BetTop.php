<?php

namespace App\Entity;

use App\Repository\BetTopRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BetTopRepository::class)
 */
class BetTop
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $predictedRanking = [];

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $validationStatus;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="betTops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=TopTen::class, inversedBy="betTops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topten;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $pointsEarned;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPredictedRanking(): ?array
    {
        return $this->predictedRanking;
    }

    public function setPredictedRanking(array $predictedRanking): self
    {
        $this->predictedRanking = $predictedRanking;

        return $this;
    }

    public function getValidationStatus(): ?string
    {
        return $this->validationStatus;
    }

    public function setValidationStatus(string $validationStatus): self
    {
        $this->validationStatus = $validationStatus;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
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

    public function getTopten(): ?TopTen
    {
        return $this->topten;
    }

    public function setTopten(?TopTen $topten): self
    {
        $this->topten = $topten;

        return $this;
    }

    public function getPointsEarned(): ?int
    {
        return $this->pointsEarned;
    }

    public function setPointsEarned(?int $pointsEarned): self
    {
        $this->pointsEarned = $pointsEarned;

        return $this;
    }
}
