<?php

/** @noinspection PhpUnused */

namespace App\Entity;

use App\DataTransferObject\PlayerData;
use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
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

    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'playerList')]
    private Collection $gameList;

    #[ORM\ManyToMany(targetEntity: League::class, mappedBy: 'playerList')]
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

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        $this->gameList->removeElement($game);

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
            $league->addPlayer($this);
        }

        return $this;
    }

    public function removeLeague(League $league): static
    {
        if ($this->leagueList->removeElement($league)) {
            $league->removePlayer($this);
        }

        return $this;
    }

    public function initFromData(EntityManagerInterface $entityManager, PlayerData $data): void
    {
        $this->setName($data->name);
    }
}