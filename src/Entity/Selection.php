<?php

namespace App\Entity;

use App\Repository\SelectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SelectionRepository::class)
 */
class Selection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $selectedAway;

    /**
     * @ORM\Column(type="integer")
     */
    private $selectedHome;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="selections")
     */
    private $teams;

    /**
     * @ORM\ManyToOne(targetEntity=League::class, inversedBy="selections")
     */
    private $leagues;

    public function __construct() {
        $this->selectedAway = 0;
        $this->selectedHome = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSelectedAway(): ?int
    {
        return $this->selectedAway;
    }

    public function setSelectedAway(?int $selectedAway): self
    {
        $this->selectedAway = $selectedAway;

        return $this;
    }

    public function getSelectedHome(): ?int
    {
        return $this->selectedHome;
    }

    public function setSelectedHome(?int $selectedHome): self
    {
        $this->selectedHome = $selectedHome;

        return $this;
    }

    public function getTeams(): ?Team
    {
        return $this->teams;
    }

    public function setTeams(?Team $teams): self
    {
        $this->teams = $teams;

        return $this;
    }

    public function getLeagues(): ?League
    {
        return $this->leagues;
    }

    public function setLeagues(?League $leagues): self
    {
        $this->leagues = $leagues;

        return $this;
    }
}
