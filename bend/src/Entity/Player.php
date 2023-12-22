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

    #[ORM\OneToMany(mappedBy: 'p1Id', targetEntity: Game::class, orphanRemoval: true)]
    private Collection $gameList;

    public function __construct()
    {
        $this->gameList = new ArrayCollection();
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

    public function addGameList(Game $gameList): static
    {
        if (!$this->gameList->contains($gameList)) {
            $this->gameList->add($gameList);
            $gameList->setP1Id($this);
        }

        return $this;
    }

    public function removeGameList(Game $gameList): static
    {
        if ($this->gameList->removeElement($gameList)) {
            // set the owning side to null (unless already changed)
            if ($gameList->getP1Id() === $this) {
                $gameList->setP1Id(null);
            }
        }

        return $this;
    }
}
