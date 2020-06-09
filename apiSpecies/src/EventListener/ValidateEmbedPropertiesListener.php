<?php

namespace App\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Document\Species;

class ValidateEmbedPropertiesListener
{
	public function preUpdate(LifecycleEventArgs $args)
	{
		$species = $args->getDocument();
		if(!$species instanceof Species) return;

		if(empty($species->getKind())) return;

		switch($species->getKind())
		{
			case 'fish': $this->validateFishProperties($species);
			break;
			case 'plant': $this->validatePlantProperties($species);
			break;
			case 'invertebrate': $this->validateInvertebrateProperties($species);
			break;
		}
	}

	private function validateFishProperties(Species $species)
	{
		if(!empty($species->getInvertebrateSpecs())) throw new BadRequestHttpException(sprintf("A %s can not have a %s property", $species->getKind(), 'invertebrateSpecs' ));
		if(!empty($species->getPlantSpecs())) throw new BadRequestHttpException(sprintf("A %s can not have a %s property", $species->getKind(), 'plantSpecs' ));
	}

	private function validatePlantProperties(Species $species)
	{
		if(!empty($species->getInvertebrateSpecs())) throw new BadRequestHttpException(sprintf("A %s can not have a %s property", $species->getKind(), 'invertebrateSpecs' ));
		if(!empty($species->getAnimalSpecs())) throw new BadRequestHttpException(sprintf("A %s can not have a %s property", $species->getKind(), 'animalSpecs' ));
		if(!empty($species->getAnimalBehaviour())) throw new BadRequestHttpException(sprintf("A %s can not have a %s property", $species->getKind(), 'animalBehaviour' ));
	}

	private function validateInvertebrateProperties(Species $species)
	{
		if(!empty($species->getPlantSpecs())) throw new BadRequestHttpException(sprintf("A %s can not have a %s property", $species->getKind(), 'plantSpecs' ));
	}
}