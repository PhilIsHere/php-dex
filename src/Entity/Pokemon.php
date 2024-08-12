<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $height = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $pokedexId = null;

    #[ORM\ManyToMany(targetEntity: PokemonTypes::class, inversedBy: 'pokemon')]
    private Collection $pokemonType;

    public function __construct() {
        $this->pokemonType = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int {
        return $this->height;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight(int $height): self {
        $this->height = $height;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPokedexId(): ?int {
        return $this->pokedexId;
    }

    /**
     * @param int $pokedexId
     * @return $this
     */
    public function setPokedexId(int $pokedexId): self {
        $this->pokedexId = $pokedexId;

        return $this;
    }

    /**
     * @return Collection<int, PokemonTypes>
     */
    public function getPokemonType(): Collection {
        return $this->pokemonType;
    }

    /**
     * @param PokemonTypes $pokemonType
     * @return $this
     */
    public function addPokemonType(PokemonTypes $pokemonType): self {
        if (!$this->pokemonType->contains($pokemonType)) {
            $this->pokemonType->add($pokemonType);
        }

        return $this;
    }

    /**
     * @param PokemonTypes $pokemonType
     * @return $this
     */
    public function removePokemonType(PokemonTypes $pokemonType): self {
        $this->pokemonType->removeElement($pokemonType);

        return $this;
    }

}
