<?php

namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

use App\Document\Species;
use App\Service\CompletionChecker;

class ValidateSpeciesController
{

	private $completionChecker;

	public function __construct(CompletionChecker $completionChecker)
	{
		$this->completionChecker = $completionChecker;
	}

	/**
	 * @Route(
	 *     name="api_patch_species_validation_state",
	 *     path="/api/species/{id}/validate",
	 *     methods={"PATCH"},
	 *     defaults={
	 *         "_api_resource_class"=Species::class,
	 *         "_api_item_operation_name"="patch_validation_state"
	 *     }
	 * )
	 */
	public function patchValidationState(Species $data): Species
	{
		if($_ENV["APP_ENV"] != 'prod') return $data;
		if($data->getValidationState() !== 'published') return $data;
		if(!$this->completionChecker->check($data)) throw  new ConflictHttpException('Species has empty element. Can not be published.');
		return $data;
	}
}