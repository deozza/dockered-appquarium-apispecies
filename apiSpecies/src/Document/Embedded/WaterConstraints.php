<?php

namespace App\Document\Embedded;

use ApiPlatform\Core\Annotation\ApiProperty;
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

class WaterConstraints
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
     * @ApiProperty(iri="https://schema.org/minValue")
     * @ODM\Field(type="float")
     * @Groups({"species:read", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min=0,
     *     max=14
     * )
     * @Assert\LessThan(
     *     propertyPath="phMax"
     * )
     */
    private $phMin;

    /**
     * @ApiProperty(iri="https://schema.org/maxValue")
     * @ODM\Field(type="float")
     * @Groups({"species:read", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min=0,
     *     max=14
     * )
     * @Assert\GreaterThan(
     *     propertyPath="phMin"
     * )
     */
    private $phMax;

    /**
     * @ApiProperty(iri="https://schema.org/minValue")
     * @ODM\Field(type="integer")
     * @Groups({"species:read", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min=0,
     *     max=50
     * )
     * @Assert\LessThan(
     *     propertyPath="ghMax"
     * )
     */
    private $ghMin;

    /**
     * @ApiProperty(iri="https://schema.org/maxValue")
     * @ODM\Field(type="integer")
     * @Groups({"species:read", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min=0,
     *     max=50
     * )
     * @Assert\GreaterThan(
     *     propertyPath="ghMin"
     * )
     */
    private $ghMax;

    /**
     * @ApiProperty(iri="https://schema.org/minValue")
     * @ODM\Field(type="integer")
     * @Groups({"species:read", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min=0,
     *     max=50
     * )
     * @Assert\LessThan(
     *     propertyPath="tempMax"
     * )
     */
    private $tempMin;

    /**
     * @ApiProperty(iri="https://schema.org/maxValue")
     * @ODM\Field(type="integer")
     * @Groups({"species:read", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min=0,
     *     max=50
     * )
     * @Assert\GreaterThan(
     *     propertyPath="tempMin"
     * )
     */
    private $tempMax;

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

    public function getPhMin(): float
    {
        return $this->phMin;
    }

    public function setPhMin(float $phMin): self
    {
        $this->phMin = $phMin;
        return $this;
    }

    public function getPhMax(): float
    {
        return $this->phMax;
    }

    public function setPhMax(float $phMax): self
    {
        $this->phMax = $phMax;
        return $this;
    }

    public function getGhMin(): int
    {
        return $this->ghMin;
    }

    public function setGhMin(int $ghMin): self
    {
        $this->ghMin = $ghMin;
        return $this;
    }

    public function getGhMax(): int
    {
        return $this->ghMax;
    }

    public function setGhMax(int $ghMax): self
    {
        $this->ghMax = $ghMax;
        return $this;
    }

    public function getTempMin(): int
    {
        return $this->tempMin;
    }

    public function setTempMin(int $tempMin): self
    {
        $this->tempMin = $tempMin;
        return $this;
    }

    public function getTempMax(): int
    {
        return $this->tempMax;
    }

    public function setTempMax(int $tempMax): self
    {
        $this->tempMax = $tempMax;
        return $this;
    }
}