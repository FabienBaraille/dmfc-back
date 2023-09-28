<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_get_collection", "user_get_item", "get_login"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Groups({"user_get_collection", "user_get_item", "get_login"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"get_login"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"user_get_item", "get_login"})
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user_get_collection", "user_get_item","get_login_league", "get_login"})
     */
    private $role = [];

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"user_get_collection","get_login_league"})
     */
    private $title;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"user_get_item","get_login_league"})
     */
    private $score;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $oldPosition;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"get_login_league"})
     */
    private $position;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"user_get_collection", "user_get_item"})
     */
    private $seasonPlayed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Leaderboard::class, mappedBy="User", orphanRemoval=true)
     */
    private $leaderboards;

    /**
     * @ORM\OneToMany(targetEntity=Srprediction::class, mappedBy="User", orphanRemoval=true)
     */
    private $srpredictions;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="users")
     * @Groups({"user_get_collection","user_get_item", "get_login"})
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity=League::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user_get_collection", "get_login"})
     */
    private $league;

    /**
     * @ORM\OneToMany(targetEntity=Round::class, mappedBy="user")
     */
    private $rounds;

    public function __construct()
    {
        $this->leaderboards = new ArrayCollection();
        $this->srpredictions = new ArrayCollection();
        $this->rounds = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(?array $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getOldPosition(): ?int
    {
        return $this->oldPosition;
    }

    public function setOldPosition(?int $oldPosition): self
    {
        $this->oldPosition = $oldPosition;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSeasonPlayed(): ?int
    {
        return $this->seasonPlayed;
    }

    public function setSeasonPlayed(?int $seasonPlayed): self
    {
        $this->seasonPlayed = $seasonPlayed;

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
            $leaderboard->setUser($this);
        }

        return $this;
    }

    public function removeLeaderboard(Leaderboard $leaderboard): self
    {
        if ($this->leaderboards->removeElement($leaderboard)) {
            // set the owning side to null (unless already changed)
            if ($leaderboard->getUser() === $this) {
                $leaderboard->setUser(null);
            }
        }

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
            $srprediction->setUser($this);
        }

        return $this;
    }

    public function removeSrprediction(Srprediction $srprediction): self
    {
        if ($this->srpredictions->removeElement($srprediction)) {
            // set the owning side to null (unless already changed)
            if ($srprediction->getUser() === $this) {
                $srprediction->setUser(null);
            }
        }

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

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
            $round->setUser($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getUser() === $this) {
                $round->setUser(null);
            }
        }

        return $this;
    }

    
}
