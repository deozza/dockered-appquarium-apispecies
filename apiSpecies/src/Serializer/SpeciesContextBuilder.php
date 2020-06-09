<?php


namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SpeciesContextBuilder implements SerializerContextBuilderInterface
{
	private $decorated;
	private $authorizationChecker;
	public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->decorated = $decorated;
		$this->authorizationChecker = $authorizationChecker;
	}
	public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
	{
		$context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
		$isEditor = $this->authorizationChecker->isGranted('ROLE_ADMIN') ||
					$this->authorizationChecker->isGranted('ROLE_FISH_EDITOR') ||
					$this->authorizationChecker->isGranted('ROLE_PLANT_EDITOR') ||
					$this->authorizationChecker->isGranted('ROLE_INVERTEBRATE_EDITOR');

		if(!$normalization)
		{
			$context['groups'][] = 'species:write';
			return $context;
		}

		if($isEditor)
		{
			$context['groups'][] = 'species:read:editor';
			return $context;
		}

		if($this->authorizationChecker->isGranted('ROLE_USER'))
		{
			$context['groups'][] = 'species:read:user';
			return $context;
		}

		return $context;
	}

}