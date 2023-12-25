<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'p1Id', targetEntity: Game::class, orphanRemoval: true)]  // TODO: p2Id
    private Collection $gameList;

    #[ORM\ManyToMany(targetEntity: League::class, inversedBy: 'playerList')]
    private Collection $leagueList;

    public function __construct()
    {
        $this->gameList = new ArrayCollection();
        $this->leagueList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGameList(): Collection
    {
        return $this->gameList;
    }

    public function addGameToP1(Game $game): static
    {
        if (!$this->gameList->contains($game)) {
            $this->gameList->add($game);
            $game->setP1Id($this);
        }

        return $this;
    }

    public function addGameToP2(Game $game): static
    {
        if (!$this->gameList->contains($game)) {
            $this->gameList->add($game);
            $game->setP2Id($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->gameList->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getP1Id() === $this) {
                $game->setP1Id(null);
            } elseif ($game->getP2Id() === $this) {
                $game->setP2Id(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, League>
     */
    public function getLeagueList(): Collection
    {
        return $this->leagueList;
    }

    public function addLeague(League $league): static
    {
        if (!$this->leagueList->contains($league)) {
            $this->leagueList->add($league);
        }

        return $this;
    }

    public function removeLeague(League $league): static
    {
        $this->leagueList->removeElement($league);

        return $this;
    }
}
