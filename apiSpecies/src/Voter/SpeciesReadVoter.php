<?php

namespace App\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Document\Species;

class SpeciesReadVoter extends Voter
{
	private $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	protected function supports($attribute, $subject)
	{
		return in_array($attribute, ['SPECIES_READ']) && $subject instanceof Species;
	}

	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		$user = $token->getUser();
		if(!$user instanceof UserInterface) return false;
		if($this->security->isGranted('ROLE_ADMIN')) return true;


		switch ($subject->getKind())
		{
			case 'fish' : if(!$this->security->isGranted('ROLE_FISH_EDITOR')) return false;
			break;
			case 'plant': if(!$this->security->isGranted('ROLE_PLANT_EDITOR')) return false;
			break;
			case 'invertebrate': if(!$this->security->isGranted('ROLE_INVERTEBRATE_EDITOR')) return false;
			break;
		}
		return true;
	}
}