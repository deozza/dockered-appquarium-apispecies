<?php


namespace App\Document\Embedded;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

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

class Image
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

	private $filePath;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read", "species:read:collection"})
	 */
	private $rawData;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read"})
	 */
	private $credit;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read"})
	 */
	private $filename;

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

	public function getFilePath(): ?string
	{
		return $this->filePath;
	}

	public function setFilePath(string $filePath): self
	{
		$this->filePath = $filePath;
		return $this;
	}

	public function getCredit(): ?string
	{
		return $this->credit;
	}

	public function setCredit(string $credit): self
	{
		$this->credit = $credit;
		return $this;
	}

	public function getFilename(): ?string
	{
		return $this->filename;
	}

	public function setFilename(string $filename): self
	{
		$this->filename = $filename;
		return $this;
	}

	public function getRawData(): string
	{
		return $this->rawData;
	}

	public function setRawData(string $data): self
	{
		$this->rawData = $data;
		return $this;
	}
}