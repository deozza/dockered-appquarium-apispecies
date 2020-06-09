<?php

namespace App\Document;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
	private $roles;
	private $id;

	public function __construct(array $dataFromMS)
	{
		$this->setId($dataFromMS['id']);
		$this->setRoles($dataFromMS['roles']);
	}

	public function setId(string $id): self
	{
		$this->id = $id;
		return $this;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;
		return $this;
	}

	public function getRoles(): array
	{
		return $this->roles;
	}

	public function eraseCredentials()
	{
	}

	public function getPassword()
	{
	}

	public function getSalt()
	{
	}

	public function getUsername()
	{
	}
}