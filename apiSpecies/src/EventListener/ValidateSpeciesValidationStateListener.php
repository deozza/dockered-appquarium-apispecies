<?php

namespace App\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Document\Species;

class ValidateSpeciesValidationStateListener
{
	public function preUpdate(LifecycleEventArgs $args)
	{
		$species = $args->getDocument();
		if(!$species instanceof Species) return;
		if(!in_array($species->getValidationState(), ['posted', 'published'])) throw new BadRequestHttpException(sprintf("Valid states are %s . %s is not valid",json_encode(['posted', 'published']), $species->getValidationState() ));
	}
}