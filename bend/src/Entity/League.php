<?php

namespace App\Entity;

use App\Repository\LeagueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeagueRepository::class)]
class League
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'league', targetEntity: Game::class)]
    private Collection $gameList;

    #[ORM\ManyToMany(targetEntity: Player::class, mappedBy: 'leagueList')]
    private Collection $playerList;

    public function __construct()
    {
        $this->gameList = new ArrayCollection();
        $this->playerList = new ArrayCollection();
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

    public function addGame(Game $game): static
    {
        if (!$this->gameList->contains($game)) {
            $this->gameList->add($game);
            $game->setLeague($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->gameList->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getLeague() === $this) {
                $game->setLeague(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayerList(): Collection
    {
        return $this->playerList;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->playerList->contains($player)) {
            $this->playerList->add($player);
            $player->addLeague($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->playerList->removeElement($player)) {
            $player->removeLeague($this);
        }

        return $this;
    }
}
