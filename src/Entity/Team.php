<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @UniqueEntity(fields="trigram", message="Ce trigramme existe déjà.")
 * @UniqueEntity(fields="name", message="Ce nom d'équipe existe déjà.")
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_get_item", "games_get_collection", "teams_get_collection","update_dmfc"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3, unique=true)
     * @Groups({"user_get_collection","user_get_item", "teams_get_collection", "games_get_collection", "games_get_post", "leagues_get_users"})
     * @Assert\NotBlank
     */
    private $trigram;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"user_get_collection","user_get_item", "teams_get_collection", "games_get_collection", "games_get_post", "leagues_get_users"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups ({"teams_get_collection"})
     * @Assert\NotBlank
     */
    private $conference;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups ({"teams_get_collection", "user_get_item", "user_get_collection", "games_get_collection", "leagues_get_users"})
     */
    private $logo;

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

    /**
     * @ORM\OneToMany(targetEntity=Selection::class, mappedBy="teams")
     */
    private $selections;

    

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->selections = new ArrayCollection();
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

    /**
     * @return Collection<int, Selection>
     */
    public function getSelections(): Collection
    {
        return $this->selections;
    }

    public function addSelection(Selection $selection): self
    {
        if (!$this->selections->contains($selection)) {
            $this->selections[] = $selection;
            $selection->setTeams($this);
        }

        return $this;
    }

    public function removeSelection(Selection $selection): self
    {
        if ($this->selections->removeElement($selection)) {
            // set the owning side to null (unless already changed)
            if ($selection->getTeams() === $this) {
                $selection->setTeams(null);
            }
        }

        return $this;
    }
}
