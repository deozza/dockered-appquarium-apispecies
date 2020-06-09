<?php

namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Document\Species;
use App\Service\FileUploader\SpeciesFileUploaderStrategy;

class SpeciesThumbnailController  extends AbstractController
{
	private $dm;

	public function __construct(DocumentManager $dm)
	{
		$this->dm = $dm;
	}

	/**
	 * @Route(
	 *     name="api_post_species_thumbnail",
	 *     path="/api/species/{id}/thumbnail",
	 *     methods={"POST"},
	 *     defaults={
	 *         "_api_resource_class"=Species::class,
	 *         "_api_item_operation_name"="post_thumbnail"
	 *     }
	 * )
	 */
	public function postSpeciesThumbnail(Species $data, Request $request): JsonResponse
	{

		if(!empty($data->getImages()))
		{
			foreach($data->getImages() as $image)
			{
				if(!empty($image->getRawData()))
				{
					$response = new JsonResponse();
					$response->setStatusCode(400);
					$response->setContent('
					{
						  "@context": "/api/contexts/Error",
						  "@type": "hydra:Error",
						  "hydra:title": "An error occured",
						  "hydra:description": "This species already contains a thumbnail. Delete it to add a new one."
					}');

					return $response;
				}
			}
		}

		$file = $request->getContent();
		if(empty($file))
		{
			$response = new JsonResponse();
			$response->setStatusCode(400);
			$response->setContent('
        	{
				  "@context": "/api/contexts/Error",
				  "@type": "hydra:Error",
				  "hydra:title": "An error occured",
				  "hydra:description": "Your request must not be empty and should contain a file."
			}');

			return $response;
		}

		$uploader = new SpeciesFileUploaderStrategy();

		try
		{
			$image = $uploader->execute($file, $optimize = true);
			$image->setSpecies($data);
			$image->setOwner($this->getUser());

			$this->dm->persist($image);

			$data->setImage($image);

			$this->dm->flush();

			return $data;
		}
		catch(\Exception $e)
		{
			$response = new JsonResponse();
			$response->setStatusCode(400);
			$response->setContent('
        	{
				  "@context": "/api/contexts/Error",
				  "@type": "hydra:Error",
				  "hydra:title": "An error occured",
				  "hydra:description": "'.$e->getMessage().'"
			}');

			return $response;
		}
	}
}