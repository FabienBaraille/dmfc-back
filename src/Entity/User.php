<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields="username", message="Ce nom d'utilisateur existe déjà.")
 * @UniqueEntity(fields="email", message="Cet email existe déjà.")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_get_collection", "user_get_item", "leagues_get_collection", "rounds_get_collection", "leaderbord", "prediction"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"user_get_collection", "user_get_item", "leagues_get_collection", "leagues_get_users", "leaderbord", "prediction"})
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_get_item", "user_get_collection"})
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user_get_collection", "user_get_item", "leagues_get_collection","update_dmfc"})
     * @Assert\NotBlank
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"leagues_get_collection","update_dmfc"})
     */
    private $title;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"leagues_get_collection","user_get_item","user_get_collection"})
     */
    private $score;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"leagues_get_collection","user_get_item","user_get_collection"})
     */
    private $scoreTOP;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"leagues_get_collection","user_get_item","user_get_collection"})
     */
    private $scorePO;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"user_get_collection", "leagues_get_collection"})
      */
    private $oldPosition;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"user_get_item"})
     */
    private $seasonPlayed;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user_get_collection"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user_get_collection"})
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Leaderboard::class, mappedBy="User", orphanRemoval=true)
     * @Groups({"leagues_get_users", "leaderbord"})
     */
    private $leaderboards;

    /**
     * @ORM\OneToMany(targetEntity=Srprediction::class, mappedBy="User", orphanRemoval=true)
     * @Groups({"user_get_item"})
     */
    private $srpredictions;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="users")
     * @Groups({"user_get_collection","user_get_item", "leagues_get_collection", "leagues_get_users","update_dmfc"})
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity=League::class, cascade={"persist"},inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user_get_collection", "get_login", "user_get_item","leaderbord"})
     */
    private $league;

    /**
     * @ORM\OneToMany(targetEntity=Round::class, mappedBy="user")
     */
    private $rounds;

    /**
     * @ORM\OneToMany(targetEntity=BetTop::class, mappedBy="User", orphanRemoval=true)
     */
    private $betTops;

    public function __construct()
    {
        $this->leaderboards = new ArrayCollection();
        $this->srpredictions = new ArrayCollection();
        $this->rounds = new ArrayCollection();
        $this->betTops = new ArrayCollection();
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

    /**
     * @return string the hashed password for this user
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): ?array
    {
        $roles = $this->roles;
        // $roles[] = 'ROLE_JOUEUR';

        return array_unique($roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

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

    public function getScoreTOP(): ?int
    {
        return $this->scoreTOP;
    }

    public function setScoreTOP(?int $scoreTOP): self
    {
        $this->scoreTOP = $scoreTOP;

        return $this;
    }

    public function getScorePO(): ?int
    {
        return $this->scorePO;
    }

    public function setScorePO(?int $scorePO): self
    {
        $this->scorePO = $scorePO;

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
    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, BetTop>
     */
    public function getBetTops(): Collection
    {
        return $this->betTops;
    }

    public function addBetTop(BetTop $betTop): self
    {
        if (!$this->betTops->contains($betTop)) {
            $this->betTops[] = $betTop;
            $betTop->setUser($this);
        }

        return $this;
    }

    public function removeBetTop(BetTop $betTop): self
    {
        if ($this->betTops->removeElement($betTop)) {
            // set the owning side to null (unless already changed)
            if ($betTop->getUser() === $this) {
                $betTop->setUser(null);
            }
        }

        return $this;
    }

    
}
