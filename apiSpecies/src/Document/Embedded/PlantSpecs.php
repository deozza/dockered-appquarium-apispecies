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
class PlantSpecs
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
	 * @Groups({"species:read", "species:write"})
	 * @Assert\Choice(callback="loadZone")
	 * @Assert\NotBlank()
	 */
	private $zone;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 */
	private $size;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\Choice(callback="loadSoilKind")
	 * @Assert\NotBlank()
	 */
	private $soilKind;

	/**
	 * @ODM\Field(type="boolean")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 */
	private $co2;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 * @Assert\Choice(callback="loadGrowthSpeed")
	 */
	private $growthSpeed;

	/**
	 * @ODM\Field(type="boolean")
	 * @Groups({"species:read:user", "species:read:editor", "species:write"})
	 */
	private $fertilizer;

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

	public function getZone(): string
	{
		return $this->zone;
	}

	public function setZone(string $zone): self
	{
		$this->zone = $zone;
		return $this;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function setSize(int $size): self
	{
		$this->size = $size;
		return $this;
	}

	public function getSoilKind(): string
	{
		return $this->soilKind;
	}

	public function setSoilKind(string $soilKind): self
	{
		$this->soilKind = $soilKind;
		return $this;
	}

	public function getCo2(): bool
	{
		return $this->co2;
	}

	public function setCo2(bool $co2): self
	{
		$this->co2 = $co2;
		return $this;
	}

	public function getGrowthSpeed(): string
	{
		return $this->growthSpeed;
	}

	public function setGrowthSpeed(string $growthSpeed): self
	{
		$this->growthSpeed = $growthSpeed;
		return $this;
	}

	public function getFertilizer(): bool
	{
		return $this->fertilizer;
	}

	public function setFertilizer(bool $fertilizer): self
	{
		$this->fertilizer = $fertilizer;
		return $this;
	}

	public static function loadZone(): array
	{
		return ['front','middle','back', 'surface', 'decor'];
	}

	public static function loadSoilKind(): array
	{
		return ['sand','sand-and-fertilizer'];
	}

	public static function loadGrowthSpeed(): array
	{
		return ['slow','normal','fast'];
	}

}