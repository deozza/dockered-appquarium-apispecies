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
class CommonLivingProperties
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
	 * @Assert\NotBlank()
	 * @Assert\Choice(callback="loadOrigin")
	 */
	private $origin;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:write"})
	 * @Assert\NotBlank()
	 * @Assert\Choice(callback="loadDifficulty")
	 */
	private $difficulty;

	/**
	 * @ODM\Field(type="string")
	 * @Assert\Length(max="255")
	 * @Groups({"species:read", "species:write"})
	 */
	private $comment;

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

	public function getOrigin(): string
	{
		return $this->origin;
	}

	public function setOrigin(string $origin): self
	{
		$this->origin = $origin;
		return $this;
	}

	public function getDifficulty(): string
	{
		return $this->difficulty;
	}

	public function setDifficulty(string $difficulty): self
	{
		$this->difficulty = $difficulty;
		return $this;
	}

	public function getComment(): ?string
	{
		return $this->comment;
	}

	public function setComment(string $comment): self
	{
		$this->comment = $comment;
		return $this;
	}

	public static function loadDifficulty(): array
	{
		return ['beginner','medium','high'];
	}

	public static function loadOrigin(): array
	{
		return ['amazonia','south-america','central-america', 'north-america', 'west-africa', 'tanganyika', 'malawi', 'victoria', 'madagascar', 'europe', 'asia', 'australia', 'new-guinea', 'cosmopolite'];
	}
}