<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_get_collection","user_get_item", "teams_get_collection", "games_get_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     * @Groups({"user_get_collection","user_get_item", "teams_get_collection", "games_get_collection"})
     */
    private $trigram;

    /**
     * @ORM\Column(type="string", length=60)
     * @Groups({"user_get_collection","user_get_item", "teams_get_collection", "games_get_collection"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups ({"teams_get_collection"})
     */
    private $conference;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups ({"teams_get_collection"})
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

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="team")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, mappedBy="team")
     */
    private $games;

    

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTeam() === $this) {
                $user->setTeam(null);
            }
        }

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
            $game->addTeam($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            $game->removeTeam($this);
        }

        return $this;
    }


}
