<?php

namespace App\Service;

use App\Document\Species;

class CompletionChecker
{

	public function check(Species $species)
	{
		$valid = $this->checkCommonBeforeValidation($species);

		if(!$valid) return false;
		switch ($species->getKind())
		{
			case 'fish'        : $valid = $this->checkFishBeforeValidation($species);break;
			case 'plant'       : $valid = $this->checkPlantBeforeValidation($species);break;
			case 'invertebrate': $valid = $this->checkInvertebrateBeforeValidation($species);break;
		}

		return $valid;
	}

	private function checkFishBeforeValidation(Species $species): bool
	{
		return !empty($species->getAnimalBehaviour()) && !empty($species->getAnimalSpecs());
	}

	private function checkPlantBeforeValidation(Species $species): bool
	{
		return !empty($species->getPlantSpecs());
	}

	private function checkInvertebrateBeforeValidation(Species $species): bool
	{
		return !empty($species->getInvertebrateSpecs());
	}

	private function checkCommonBeforeValidation(Species $species): bool
	{
		return
			!empty($species->getCommonLivingProperties()) &&
			!empty($species->getSpeciesNaming())          &&
			!empty($species->getSpeciesReproduction())    &&
			!empty($species->getWaterConstraints())       &&
			!empty($species->getImages())                ;
	}

}