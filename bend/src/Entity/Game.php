<?php

/** @noinspection PhpUnused */

namespace App\Entity;

use App\DataTransferObject\GameData;
use App\Repository\GameRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $winnerId = null;

    #[ORM\Column(length: 255)]
    private ?string $winnerName = null;

    #[ORM\Column(length: 255)]
    private ?string $result = null;

    #[ORM\ManyToOne(inversedBy: 'gameList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?League $league = null;

    #[ORM\ManyToMany(targetEntity: Player::class, mappedBy: 'gameList')]
    private Collection $playerList;

    public function __construct()
    {
        $this->playerList = new ArrayCollection();
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

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getWinnerId(): ?int
    {
        return $this->winnerId;
    }

    public function setWinnerId(int $winnerId): static
    {
        $this->winnerId = $winnerId;

        return $this;
    }

    public function getWinnerName(): ?string
    {
        return $this->winnerName;
    }

    public function setWinnerName(string $winnerName): static
    {
        $this->winnerName = $winnerName;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): static
    {
        $this->league = $league;

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
            $player->addGame($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->playerList->removeElement($player)) {
            $player->removeGame($this);
        }

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function initFromData(EntityManagerInterface $entityManager, GameData $data): void
    {
        $repository = $entityManager->getRepository(League::class);
        $league = $repository->find($data->leagueId);
        if ($league === null) {
            throw new RuntimeException('Invalid league identifier!');
        }
        $this->setLeague($league);
        $this->setDate(DateTime::createFromFormat(DateTimeInterface::ATOM, $data->date));
        $this->setWinnerId($data->winnerId);
        $this->setWinnerName($data->winnerName);
        $this->setResult($data->result);
    }
}