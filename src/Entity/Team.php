<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $trigram;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $conference;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nbSelectedHome;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $nbSelectedAway;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $ranking;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrigram(): ?string
    {
        return $this->trigram;
    }

    public function setTrigram(string $trigram): self
    {
        $this->trigram = $trigram;

        return $this;
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

    public function getConference(): ?string
    {
        return $this->conference;
    }

    public function setConference(string $conference): self
    {
        $this->conference = $conference;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getNbSelectedHome(): ?int
    {
        return $this->nbSelectedHome;
    }

    public function setNbSelectedHome(?int $nbSelectedHome): self
    {
        $this->nbSelectedHome = $nbSelectedHome;

        return $this;
    }

    public function getNbSelectedAway(): ?int
    {
        return $this->nbSelectedAway;
    }

    public function setNbSelectedAway(?int $nbSelectedAway): self
    {
        $this->nbSelectedAway = $nbSelectedAway;

        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(?int $ranking): self
    {
        $this->ranking = $ranking;

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
}
