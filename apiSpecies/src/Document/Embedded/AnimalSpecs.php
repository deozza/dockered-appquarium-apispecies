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
class AnimalSpecs
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
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 * @Assert\NotBlank()
	 */
	private $maleSize;

	/**
	 * @ODM\Field(type="integer")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\GreaterThan(0)
	 * @Assert\NotBlank()
	 */
	private $femaleSize;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\Choice(callback="loadAquariumKind")
	 * @Assert\NotBlank()
	 */
	private $aquariumKind;

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

	public function setSpecies(Species $species): void
	{
		$this->species = $species;
	}

	public function getMaleSize(): int
	{
		return $this->maleSize;
	}

	public function setMaleSize(int $maleSize): self
	{
		$this->maleSize = $maleSize;
		return $this;
	}

	public function getFemaleSize(): int
	{
		return $this->femaleSize;
	}

	public function setFemaleSize(int $femaleSize): self
	{
		$this->femaleSize = $femaleSize;
		return $this;
	}

	public function getAquariumKind(): string
	{
		return $this->aquariumKind;
	}

	public function setAquariumKind(string $aquariumKind): self
	{
		$this->aquariumKind = $aquariumKind;
		return $this;
	}

	public static function loadAquariumKind(): array
	{
		return ['specific','individual','communal'];
	}

}