<?php

namespace App\Document;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Filter\OrderFilter;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\SpeciesRepository;
use App\Document\Embedded\AnimalBehaviour;
use App\Document\Embedded\AnimalSpecs;
use App\Document\Embedded\CommonLivingProperties;
use App\Document\Embedded\Image;
use App\Document\Embedded\InvertebrateSpecs;
use App\Document\Embedded\PlantSpecs;
use App\Document\Embedded\SpeciesNaming;
use App\Document\Embedded\SpeciesReproduction;
use App\Document\Embedded\WaterConstraints;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *     			"normalization_context"={"groups"={"species:read:collection"}}
 *	 		},
 *          "post"={}
 *    },
 *     itemOperations={
 *          "get"={
 *               "security"="is_granted('SPECIES_READ', object) or object.getValidationState() == 'published'"
 * 			},
 *          "patch"={
 *     			"denormalization_context"={"groups"={"species:update"}}
 * 			},
 *          "delete"={},
 *      	"patch_validation_state"={
 *     					"route_name"="api_patch_species_validation_state",
 *     					"method"="PATCH",
 *     					"path"="/api/species/{id}/validate",
 *     					"normalization_context"={"groups"={"species:read:editor"}},
 *     					"denormalization_context"={"groups"={"species:validate"}},
 *	 		},
 *      	"post_thumbnail"={
 *     					"route_name"="api_post_species_thumbnail",
 *     					"method"="POST",
 *     					"path"="/api/species/{id}/thumbnail",
 *                      "deserialize"=false
 * 			}
 *     },
 *     normalizationContext={"groups"={"species:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"species:write"}, "swagger_definition_name"="Write", "allow_extra_attributes"=false}
 * )
 * @ApiFilter(SearchFilter::class,
 *     properties={
 *    		"kind": "exact",
 *    		"speciesNaming.scientificName": "ipartial",
 *    		"commonLivingProperties.origin": "exact",
 *    		"commonLivingProperties.difficulty": "exact",
 *	   }
 * )
 * @ApiFilter(OrderFilter::class,
 *     properties={
 *     		"speciesNaming.scientificName": "ASC",
 *     		"dateOfCreation"
 * 	   },
 *      arguments={
 *     		"orderParameterName"="order"
 * 		}
 * )
 * @ODM\Document(repositoryClass=SpeciesRepository::class)
 * @ODM\HasLifecycleCallbacks()
 * @ODM\PrePersist({
 *     "App\EventListener\Species\SetOwnerListener",
 *     "App\EventListener\Species\HasCorrespondingRoleListener",
 * })
 * @ODM\PreUpdate({
 *     "App\EventListener\Species\ValidateEmbedPropertiesListener",
 *     "App\EventListener\Species\ValidateSpeciesValidationStateListener"
 *	 })
 */
class Species
{
    /**
     * @ApiProperty(iri="https://schema.org/identifier")
     * @ODM\Id(strategy="INCREMENT",type="integer")
     * @Groups({"species:read", "species:read:collection"})
     */
    private $id;

    /**
     * @ApiProperty(iri="https://schema.org/status")
     * @ODM\Field(type="string")
     * @Groups({"species:read:editor", "species:validate"})
     */
    private $validationState;

    /**
     * @ApiProperty(iri="https://schema.org/dateCreated")
     * @ODM\Field(type="date")
     * @Groups({"species:read:editor", "species:read:collection"})
     */
    private $dateOfCreation;

    /**
     * @ApiProperty(iri="https://schema.org/dateModified")
     * @ODM\Field(type="date")
     * @Groups({"species:read:editor"})
     */
    private $lastUpdate;

    /**
     * @ApiProperty(iri="https://schema.org/category")
     * @ODM\Field(type="string")
     * @Groups({"species:read", "species:read:collection", "species:write"})
     * @Assert\NotBlank()
     * @Assert\Choice(callback="loadKinds")
     */
    private $kind;

	/**
	 * @ODM\Field(type="string")
	 * @Groups({"species:read:editor", "species:write"})
	 */
    private $owner;

    /**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\SpeciesNaming",mappedBy="species", storeAs="id")
	 * @ApiSubresource()
     * @Groups({"species:update", "species:read:collection", "species:read"})
     */
    private $speciesNaming;

    /**
     * @Assert\Valid
     * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\WaterConstraints", mappedBy="species", storeAs="id")
	 * @ApiSubresource()
	 * @Groups({"species:update", "species:read"})
     */
    private $waterConstraints;

	/**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\SpeciesReproduction", mappedBy="species", storeAs="id")
	 * @ApiSubresource()
	 * @Groups({"species:update", "species:read"})
	 */
	private $speciesReproduction;

	/**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\AnimalBehaviour", mappedBy="species", storeAs="id")
	 * @ApiSubresource()
	 * @Groups({"species:update", "species:read"})
	 */
	private $animalBehaviour;

	/**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\CommonLivingProperties", mappedBy="species", storeAs="id")
	 * @Groups({"species:update", "species:read"})
	 */
	private $commonLivingProperties;

	/**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\AnimalSpecs", mappedBy="species", storeAs="id")
	 * @ApiSubresource()
	 * @Groups({"species:update", "species:read"})
	 */
	private $animalSpecs;

	/**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\PlantSpecs", mappedBy="species", storeAs="id")
	 * @ApiSubresource()
	 * @Groups({"species:update", "species:read"})
	 */
	private $plantSpecs;

	/**
	 * @Assert\Valid
	 * @ODM\ReferenceOne(targetDocument="App\Document\Embedded\InvertebrateSpecs", mappedBy="species", storeAs="id")
	 * @ApiSubresource()
	 * @Groups({"species:update", "species:read"})
	 */
	private $invertebrateSpecs;

	/**
	 * @ODM\ReferenceMany(targetDocument="App\Document\Embedded\Image", mappedBy="species", storeAs="id")
	 * @Groups({"species:write:thumbnail", "species:read:collection", "species:read"})
	 */
	private $images;

    public function __construct()
    {
        $this->dateOfCreation = new \DateTime('now');
        $this->lastUpdate = $this->dateOfCreation;
        $this->validationState = 'posted';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setKind(string $kind): self
	{
		if(!in_array($kind, self::loadKinds())) return $this;

		$this->kind = $kind;
		return $this;
	}

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getValidationState(): string
    {
        return $this->validationState;
    }

    public function setValidationState(string $validationState): self
    {
        $this->validationState = $validationState;
        return $this;
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

    public function getSpeciesNaming(): ?SpeciesNaming
    {
        return $this->speciesNaming;
    }

    public function setSpeciesNaming(SpeciesNaming $speciesNaming): self
    {
        $this->speciesNaming = $speciesNaming;
        return $this;
    }

    public function getWaterConstraints(): ?WaterConstraints
    {
        return $this->waterConstraints;
    }

    public function setWaterConstraints(WaterConstraints $waterConstraints): self
    {
        $this->waterConstraints = $waterConstraints;
        return $this;
    }

	public function getSpeciesReproduction(): ?SpeciesReproduction
	{
		return $this->speciesReproduction;
	}

	public function setSpeciesReproduction(SpeciesReproduction $speciesReproduction): self
	{
		$this->speciesReproduction = $speciesReproduction;
		return $this;
	}

	public function getAnimalBehaviour(): ?AnimalBehaviour
	{
		return $this->animalBehaviour;
	}

	public function setAnimalBehaviour(AnimalBehaviour $animalBehaviour): self
	{
		$this->animalBehaviour = $animalBehaviour;
		return $this;
	}

	public function getCommonLivingProperties(): ?CommonLivingProperties
	{
		return $this->commonLivingProperties;
	}

	public function setCommonLivingProperties(CommonLivingProperties $commonLivingProperties): self
	{
		$this->commonLivingProperties = $commonLivingProperties;
		return $this;
	}

	public function getAnimalSpecs(): ?AnimalSpecs
	{
		return $this->animalSpecs;
	}

	public function setAnimalSpecs(AnimalSpecs $animalSpecs): self
	{
		$this->animalSpecs = $animalSpecs;
		return $this;
	}

	public function getPlantSpecs(): ?PlantSpecs
	{
		return $this->plantSpecs;
	}

	public function setPlantSpecs(PlantSpecs $plantSpecs): self
	{
		$this->plantSpecs = $plantSpecs;
		return $this;
	}

	public function getInvertebrateSpecs(): ?InvertebrateSpecs
	{
		return $this->invertebrateSpecs;
	}

	public function setInvertebrateSpecs(InvertebrateSpecs $invertebrateSpecs): self
	{
		$this->invertebrateSpecs = $invertebrateSpecs;
		return $this;
	}

	public function getImages()
	{
		return $this->images;
	}

	public function setImage(Image $image): self
	{
		$this->images[] = $image;
		return $this;
	}

    public static function loadKinds(): array
    {
        return ['fish','plant','invertebrate'];
    }
}