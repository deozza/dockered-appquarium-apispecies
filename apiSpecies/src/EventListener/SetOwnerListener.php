<?php

namespace App\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

use App\Document\Embedded\AnimalBehaviour;
use App\Document\Embedded\AnimalSpecs;
use App\Document\Embedded\CommonLivingProperties;
use App\Document\Embedded\InvertebrateSpecs;
use App\Document\Embedded\PlantSpecs;
use App\Document\Embedded\SpeciesNaming;
use App\Document\Embedded\SpeciesReproduction;
use App\Document\Embedded\WaterConstraints;
use App\Document\Species;

class SetOwnerListener
{
	private $security;
	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$object = $args->getDocument();
		if(!$object instanceof Species                 &&
			!$object instanceof SpeciesNaming          &&
			!$object instanceof AnimalBehaviour        &&
			!$object instanceof AnimalSpecs            &&
			!$object instanceof CommonLivingProperties &&
			!$object instanceof InvertebrateSpecs      &&
			!$object instanceof PlantSpecs             &&
			!$object instanceof SpeciesReproduction    &&
			!$object instanceof WaterConstraints
		)
		{
			return;
		}

		if(!empty($object->getOwner())) return;
		if(!empty($this->security->getUser())) $object->setOwner($this->security->getUser()->getId());
	}
}