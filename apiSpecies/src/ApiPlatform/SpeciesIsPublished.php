<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Extension\AggregationCollectionExtensionInterface;
use Doctrine\ODM\MongoDB\Aggregation\Builder;
use Symfony\Component\Security\Core\Security;

use App\Document\Species;

class SpeciesIsPublished implements AggregationCollectionExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(Builder $aggregationBuilder, string $resourceClass, string $operationName = null, array &$context = [])
    {
        $this->addWhere($aggregationBuilder, $resourceClass);
    }

    private function addWhere(Builder $builder, string $resourceClass)
    {
        $rights = $this->getAccessRights();

        if($resourceClass !== Species::class) return;

        if($this->security->isGranted('ROLE_ADMIN')) return;
        
        $builder->match()
            ->addOr($builder->matchExpr()
                ->field('kind')->equals('plant')
                ->field('validationState')->in($rights['plant'])
            )
            ->addOr($builder->matchExpr()
                ->field('kind')->equals('fish')
                ->field('validationState')->in($rights['fish'])
            )
            ->addOr($builder->matchExpr()
                ->field('kind')->equals('invertebrate')
                ->field('validationState')->in($rights['invertebrate'])
            );
    }

	private function getAccessRights(): array
	{
		$rights = [
			'plant'=>['published'],
			'fish'=>['published'],
			'invertebrate'=>['published']
		];

		if($this->security->isGranted('ROLE_FISH_EDITOR')) $rights['fish'] = ['published', 'posted'];
		if($this->security->isGranted('ROLE_PLANT_EDITOR')) $rights['plant'] = ['published', 'posted'];
		if($this->security->isGranted('ROLE_INVERTEBRATE_EDITOR')) $rights['invertebrate'] = ['published', 'posted'];

		return $rights;
	}
}