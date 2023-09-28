<?php

namespace App\Entity;

use App\Repository\LeagueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=LeagueRepository::class)
 */
class League
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_get_collection","get_login_league"}, {"leagues_get_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"user_get_collection","get_login_league", "leagues_get_collection"})
     */
    private $leagueName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user_get_collection","get_login_league", "leagues_get_collection"})
     */
    private $leagueDescription;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="league")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Round::class, mappedBy="league", orphanRemoval=true)
     */
    private $rounds;

    /**
     * @ORM\OneToMany(targetEntity=News::class, mappedBy="league")
     */
    private $news;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->rounds = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeagueName(): ?string
    {
        return $this->leagueName;
    }

    public function setLeagueName(string $leagueName): self
    {
        $this->leagueName = $leagueName;

        return $this;
    }

    public function getLeagueDescription(): ?string
    {
        return $this->leagueDescription;
    }

    public function setLeagueDescription(?string $leagueDescription): self
    {
        $this->leagueDescription = $leagueDescription;

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
            $user->setLeague($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getLeague() === $this) {
                $user->setLeague(null);
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
            $round->setLeague($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getLeague() === $this) {
                $round->setLeague(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setLeague($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getLeague() === $this) {
                $news->setLeague(null);
            }
        }

        return $this;
    }
}
