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
 *              "denormalization_context"={"groups"={"species:write"}}
 *          },
 *          "delete"={},
 *     },
 *     normalizationContext={"groups"={"species:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"species:write"}, "swagger_definition_name"="Write", "allow_extra_attributes"=false}
 * )
 * @ODM\Document()
 * @ODM\HasLifecycleCallbacks()
 */
class SpeciesNaming
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
	 * @Groups({"species:read", "species:read:collection", "species:write"})
     * @Assert\NotBlank()
	 */
    private $scientificName;

    /**
     * @ODM\Field(type="collection")
	 * @Groups({"species:read", "species:read:collection", "species:write"})
	 */
    private $commonNames;

    /**
     * @ODM\Field(type="collection")
	 * @Groups({"species:read", "species:write"})
	 */
    private $oldNames;

    /**
     * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\NotBlank()
     */
    private $familyName;

    /**
     * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 */
    private $genreName;

    /**
     * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 */
    private $groupName;

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

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(string $scientificName): self
    {
        $this->scientificName = $scientificName;
        return $this;
    }

    public function getCommonNames(): ?array
    {
        return $this->commonNames;
    }

    public function setCommonNames(?array $commonNames): self
    {
        $this->commonNames = $commonNames;
        return $this;
    }

    public function getOldNames(): ?array
    {
        return $this->oldNames;
    }

    public function setOldNames(?array $oldNames): self
    {
        $this->oldNames = $oldNames;
        return $this;
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;
        return $this;
    }

    public function getGenreName(): ?string
    {
        return $this->genreName;
    }

    public function setGenreName(string $genreName): self
    {
        $this->genreName = $genreName;
        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): self
    {
        $this->groupName = $groupName;
        return $this;
    }
}