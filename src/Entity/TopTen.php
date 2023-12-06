<?php

namespace App\Entity;

use App\Repository\TopTenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TopTenRepository::class)
 */
class TopTen
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"topten_get_collection", "topten_get_post"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     * @Groups({"topten_get_collection", "topten_get_post"})
     */
    private $conference;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"topten_get_collection", "topten_get_post"})
     */
    private $deadline;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"topten_get_collection", "topten_get_post"})
     */
    private $results = [];

    /**
     * @ORM\ManyToMany(targetEntity=Team::class)
     * @Groups({"topten_get_collection", "topten_get_post"})
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity=Round::class, inversedBy="topTens")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"topten_get_collection", "topten_get_post"})
     */
    private $round;

    /**
     * @ORM\OneToMany(targetEntity=BetTop::class, mappedBy="topten", orphanRemoval=true)
     * @Groups({"topten_get_collection"})
     */
    private $betTops;

    public function __construct()
    {
        $this->team = new ArrayCollection();
        $this->betTops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getResults(): ?array
    {
        return $this->results;
    }

    public function setResults(?array $results): self
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->team->contains($team)) {
            $this->team[] = $team;
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->team->removeElement($team);

        return $this;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): self
    {
        $this->round = $round;

        return $this;
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
            $betTop->setTopten($this);
        }

        return $this;
    }

    public function removeBetTop(BetTop $betTop): self
    {
        if ($this->betTops->removeElement($betTop)) {
            // set the owning side to null (unless already changed)
            if ($betTop->getTopten() === $this) {
                $betTop->setTopten(null);
            }
        }

        return $this;
    }
}
