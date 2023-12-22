<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'gameList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $p1Id = null;

    #[ORM\ManyToOne(inversedBy: 'gameList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $p2Id = null;

    #[ORM\Column]
    private ?int $p1Points = null;

    #[ORM\Column]
    private ?int $p2Points = null;

    #[ORM\ManyToOne(inversedBy: 'gameList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $winnerId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getP1Id(): ?Player
    {
        return $this->p1Id;
    }

    public function setP1Id(?Player $p1Id): static
    {
        $this->p1Id = $p1Id;

        return $this;
    }

    public function getP2Id(): ?Player
    {
        return $this->p2Id;
    }

    public function setP2Id(?Player $p2Id): static
    {
        $this->p2Id = $p2Id;

        return $this;
    }

    public function getP1Points(): ?int
    {
        return $this->p1Points;
    }

    public function setP1Points(int $p1Points): static
    {
        $this->p1Points = $p1Points;

        return $this;
    }

    public function getP2Points(): ?int
    {
        return $this->p2Points;
    }

    public function setP2Points(int $p2Points): static
    {
        $this->p2Points = $p2Points;

        return $this;
    }

    public function getWinnerId(): ?Player
    {
        return $this->winnerId;
    }

    public function setWinnerId(?Player $winnerId): static
    {
        $this->winnerId = $winnerId;

        return $this;
    }
}
