<?php

namespace App\Document\Embedded;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use App\Document\Species;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "post"={}
 *    },
 *     itemOperations={
 *          "get"={},
 *          "patch"={
 *     			"denormalization_context"={"groups"={"species:write"}}
 * 			},
 *          "delete"={},
 *     },
 *     normalizationContext={"groups"={"species:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"species:write"}, "swagger_definition_name"="Write", "allow_extra_attributes"=false}
 * )
 * @ODM\Document()
 * @ODM\HasLifecycleCallbacks()
 */
class AnimalBehaviour
{
    /**
     * @ODM\Id(strategy="INCREMENT", type="integer")
     */
    private $id;

	/**
	 * @ODM\ReferenceOne(targetDocument="App\Document\Species", inversedBy="speciesNaming", storeAs="id")
	 * @Groups({"species:write"})
	 */
	private $species;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read:editor"})
	 */
	private $owner;

	/**
	 * @ODM\Field(type="date")
	 * @Groups({"species:read:editor"})
	 */
	private $dateOfCreation;

	/**
	 * @ODM\Field(type="date")
	 * @Groups({"species:read:editor"})
	 */
	private $lastUpdate;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\NotBlank()
	 * @Assert\Choice(callback="loadBehaviour")
	 */
	private $intraspecificBehaviour;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\NotBlank()
	 * @Assert\Choice(callback="loadBehaviour")
	 */
	private $extraspecificBehaviour;

	/**
	 * @ODM\Field(type="float")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\Range(
	 *     min=0,
	 *     max=1
	 * )
	 */
	private $maleFemaleRatio;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 * @Assert\NotBlank()
	 */
	private $nbMinGroup;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\GreaterThan(0)
	 */
	private $maleTerritory;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\GreaterThan(0)
	 */
	private $femaleTerritory;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\NotBlank()
	 * @Assert\Choice(callback="loadZone")
	 */
	private $lifeZone;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 * @Assert\NotBlank()
	 */
	private $spaceOccupied;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 * @Assert\NotBlank()
	 */
	private $aquariumMinWidth;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 * @Assert\NotBlank()
	 */
	private $aquariumMinVolume;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\NotBlank()
 	 * @Assert\Choice(callback="loadAlimentation")
	 */
	private $alimentation;

	public function __construct()
	{
		$this->dateOfCreation = new \DateTime('now');
		$this->lastUpdate = $this->dateOfCreation;
	}

	public function getId(): int
	{
		return $this->id;
	}


	public function getDateOfCreation(): \DateTime
	{
		return $this->dateOfCreation;
	}

	public function getLastUpdate(): \DateTime
	{
		return $this->lastUpdate;
	}

	/**
	 * @ODM\PreFlush
	 */
	public function setLastUpdate(): self
	{
		$this->lastUpdate = new \DateTime('now');
		return $this;
	}

	public function getOwner(): ?string
	{
		return $this->owner;
	}

	public function setOwner(string $user): self
	{
		$this->owner = $user;
		return $this;
	}

	public function getSpecies(): Species
	{
		return $this->species;
	}

	public function setSpecies($species): void
	{
		$this->species = $species;
	}

	public function getIntraspecificBehaviour(): string
	{
		return $this->intraspecificBehaviour;
	}

	public function setIntraspecificBehaviour(string $intraspecificBehaviour): self
	{
		$this->intraspecificBehaviour = $intraspecificBehaviour;
		return $this;
	}

	public function getExtraspecificBehaviour(): string
	{
		return $this->extraspecificBehaviour;
	}

	public function setExtraspecificBehaviour(string $extraspecificBehaviour): self
	{
		$this->extraspecificBehaviour = $extraspecificBehaviour;
		return $this;
	}

	public function getMaleFemaleRatio(): ?float
	{
		return $this->maleFemaleRatio;
	}

	public function setMaleFemaleRatio(float $maleFemaleRatio): self
	{
		$this->maleFemaleRatio = $maleFemaleRatio;
		return $this;
	}

	public function getNbMinGroup(): int
	{
		return $this->nbMinGroup;
	}

	public function setNbMinGroup(int $nbMinGroup): self
	{
		$this->nbMinGroup = $nbMinGroup;
		return $this;
	}

	public function getMaleTerritory(): ?int
	{
		return $this->maleTerritory;
	}

	public function setMaleTerritory(int $maleTerritory): self
	{
		$this->maleTerritory = $maleTerritory;
		return $this;
	}

	public function getFemaleTerritory(): ?int
	{
		return $this->femaleTerritory;
	}

	public function setFemaleTerritory(int $femaleTerritory): self
	{
		$this->femaleTerritory = $femaleTerritory;
		return $this;
	}

	public function getLifeZone(): string
	{
		return $this->lifeZone;
	}

	public function setLifeZone(string $lifeZone): self
	{
		$this->lifeZone = $lifeZone;
		return $this;
	}

	public function getSpaceOccupied(): int
	{
		return $this->spaceOccupied;
	}

	public function setSpaceOccupied(int $spaceOccupied): self
	{
		$this->spaceOccupied = $spaceOccupied;
		return $this;
	}

	public function getAquariumMinWidth(): int
	{
		return $this->aquariumMinWidth;
	}

	public function setAquariumMinWidth(int $aquariumMinWidth): self
	{
		$this->aquariumMinWidth = $aquariumMinWidth;
		return $this;
	}

	public function getAquariumMinVolume(): int
	{
		return $this->aquariumMinVolume;
	}

	public function setAquariumMinVolume(int $aquariumMinVolume): self
	{
		$this->aquariumMinVolume = $aquariumMinVolume;
		return $this;
	}

	public function getAlimentation(): string
	{
		return $this->alimentation;
	}

	public function setAlimentation(string $alimentation): self
	{
		$this->alimentation = $alimentation;
		return $this;
	}

	public static function loadDifficulty(): array
	{
		return ['beginner','medium','high'];
	}

	public static function loadBehaviour(): array
	{
		return ['calm','timid','aggressive'];
	}

	public static function loadZone(): array
	{
		return ['ground','medium','surface', 'medium-surface', 'medium-ground', 'rocks', 'snails', 'everywhere'];
	}

	public static function loadAlimentation(): array
	{
		return ['carnivorous','vegetarian','omnivorous', 'omnivorous-vegetarian', 'omnivorous-carnivorous'];
	}
}